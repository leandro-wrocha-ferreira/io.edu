<?php

namespace tests\unit\usecases\commercial;

use app\domain\commercial\value_objects\BillingModel;
use app\domain\commercial\value_objects\SubscriptionInterval;
use app\domain\course\Category;
use app\domain\course\Course;
use app\domain\exceptions\ConflictException;
use app\domain\exceptions\NotFoundException;
use app\domain\exceptions\ValidationException;
use app\domain\finance\CostCenter;
use app\domain\finance\PaymentAccount;
use app\domain\finance\PaymentConfiguration;
use app\usecases\commercial\CreateProductUseCase;
use PHPUnit\Framework\TestCase;
use tests\unit\mocks\repositories\MockCategoryRepository;
use tests\unit\mocks\repositories\MockCostCenterRepository;
use tests\unit\mocks\repositories\MockCourseRepository;
use tests\unit\mocks\repositories\MockPaymentConfigurationRepository;
use tests\unit\mocks\repositories\MockProductPriceVersionRepository;
use tests\unit\mocks\repositories\MockProductRepository;
use tests\unit\mocks\repositories\MockTransactionManager;

/**
 * Unit tests for CreateProductUseCase.
 */
class CreateProductUseCaseTest extends TestCase
{
	private MockProductRepository $product_repository;
	private MockCourseRepository $course_repository;
	private MockCategoryRepository $category_repository;
	private MockProductPriceVersionRepository $price_version_repository;
	private MockPaymentConfigurationRepository $payment_configuration_repository;
	private MockCostCenterRepository $cost_center_repository;
	private MockTransactionManager $transaction_manager;
	private CreateProductUseCase $use_case;
	private Course $course;
	private PaymentConfiguration $payment_config;
	private CostCenter $cost_center;

	protected function setUp(): void
	{
		parent::setUp();
		$this->product_repository = new MockProductRepository();
		$this->course_repository = new MockCourseRepository();
		$this->category_repository = new MockCategoryRepository();
		$this->price_version_repository = new MockProductPriceVersionRepository();
		$this->payment_configuration_repository = new MockPaymentConfigurationRepository();
		$this->cost_center_repository = new MockCostCenterRepository();
		$this->transaction_manager = new MockTransactionManager();

		$this->use_case = new CreateProductUseCase(
			$this->product_repository,
			$this->course_repository,
			$this->transaction_manager,
			$this->price_version_repository,
			$this->payment_configuration_repository,
			$this->cost_center_repository
		);

		$category = $this->category_repository->create(Category::create('Tecnologia', 'tecnologia'));
		$this->course = $this->course_repository->create(Course::create($category->get_id(), 'Node.js Avançado', 'nodejs-avancado'));

		$this->payment_config = $this->payment_configuration_repository->save(
			PaymentConfiguration::create('Gateway Padrão', 1, ['credit_card', 'pix'])
		);

		$this->cost_center = $this->cost_center_repository->save(
			CostCenter::create('Cursos Online')
		);
	}

	public function test_create_free_product_success(): void
	{
		$product = $this->use_case->execute(
			$this->course->get_id(),
			true,
			null
		);

		$this->assertNotNull($product->get_id());
		$this->assertSame($this->course->get_id(), $product->get_course_id());
		$this->assertTrue($product->is_free());
		$this->assertNull($product->get_price());
		$this->assertTrue($product->get_billing_model()->is_one_time());
		$this->assertSame(1, $product->get_max_installments()->get_limit());
		$this->assertTrue($this->transaction_manager->was_committed());

		// Free product does not generate version 1 price record
		$version = $this->price_version_repository->find_latest_by_product_id($product->get_id());
		$this->assertNull($version);
	}

	public function test_create_paid_product_generates_version_snapshot(): void
	{
		$product = $this->use_case->execute(
			$this->course->get_id(),
			false,
			29900, // R$ 299,00
			BillingModel::ONE_TIME,
			6,
			null,
			null,
			['credit_card', 'pix'],
			$this->payment_config->get_id(),
			$this->cost_center->get_id(),
			42 // user id
		);

		$this->assertNotNull($product->get_id());
		$this->assertFalse($product->is_free());
		$this->assertSame(29900, $product->get_price()->get_cents());
		$this->assertSame(6, $product->get_max_installments()->get_limit());
		$this->assertSame($this->payment_config->get_id(), $product->get_payment_configuration_id());
		$this->assertSame($this->cost_center->get_id(), $product->get_cost_center_id());

		// Verify version 1 snapshot
		$version = $this->price_version_repository->find_latest_by_product_id($product->get_id());
		$this->assertNotNull($version);
		$this->assertSame(1, $version->get_version_number());
		$this->assertNull($version->get_previous_price());
		$this->assertSame(29900, $version->get_new_price()->get_cents());
		$this->assertSame(42, $version->get_created_by());

		$snapshot = $version->get_snapshot();
		$this->assertSame($product->get_id(), $snapshot['product_id']);
		$this->assertSame($this->course->get_title(), $snapshot['course_title']);
		$this->assertSame(29900, $snapshot['price_in_cents']);
	}

	public function test_create_subscription_product_success(): void
	{
		$product = $this->use_case->execute(
			$this->course->get_id(),
			false,
			4990, // R$ 49,90/mês
			BillingModel::SUBSCRIPTION,
			1,
			SubscriptionInterval::MONTHLY,
			12,
			['credit_card'],
			$this->payment_config->get_id()
		);

		$this->assertTrue($product->get_billing_model()->is_subscription());
		$this->assertSame(SubscriptionInterval::MONTHLY, (string) $product->get_subscription_interval());
		$this->assertSame(12, $product->get_max_billing_cycles());
		$this->assertSame(1, $product->get_max_installments()->get_limit());
	}

	public function test_create_product_for_non_existing_course_throws_not_found(): void
	{
		$this->expectException(NotFoundException::class);
		$this->use_case->execute(9999);
	}

	public function test_create_product_when_course_already_has_product_throws_conflict(): void
	{
		$this->use_case->execute($this->course->get_id(), true, null);

		$this->expectException(ConflictException::class);
		$this->use_case->execute($this->course->get_id(), false, 1000);
	}

	public function test_create_product_with_inactive_payment_configuration_throws_validation_exception(): void
	{
		$inactive_config = $this->payment_configuration_repository->save(
			PaymentConfiguration::create('Config Inativa', 1, ['pix'], null, false)
		);

		$this->expectException(ValidationException::class);
		$this->use_case->execute(
			$this->course->get_id(),
			false,
			10000,
			BillingModel::ONE_TIME,
			1,
			null,
			null,
			['pix'],
			$inactive_config->get_id()
		);
	}

	public function test_create_product_with_inactive_cost_center_throws_validation_exception(): void
	{
		$inactive_cc = $this->cost_center_repository->save(
			CostCenter::create('Centro Inativo', null, false)
		);

		$this->expectException(ValidationException::class);
		$this->use_case->execute(
			$this->course->get_id(),
			false,
			10000,
			BillingModel::ONE_TIME,
			1,
			null,
			null,
			['pix'],
			null,
			$inactive_cc->get_id()
		);
	}
}
