<?php

namespace tests\unit\usecases\commercial;

use app\domain\commercial\Product;
use app\domain\commercial\ProductPriceVersion;
use app\domain\commercial\value_objects\BillingModel;
use app\domain\commercial\value_objects\EnabledPaymentMethods;
use app\domain\commercial\value_objects\InstallmentLimit;
use app\domain\commercial\value_objects\Price;
use app\domain\course\Category;
use app\domain\course\Course;
use app\domain\exceptions\NotFoundException;
use app\domain\exceptions\ValidationException;
use app\domain\finance\CostCenter;
use app\domain\finance\PaymentConfiguration;
use app\usecases\commercial\UpdateProductCommercialConditionsUseCase;
use PHPUnit\Framework\TestCase;
use tests\unit\mocks\repositories\MockCategoryRepository;
use tests\unit\mocks\repositories\MockCostCenterRepository;
use tests\unit\mocks\repositories\MockCourseRepository;
use tests\unit\mocks\repositories\MockPaymentConfigurationRepository;
use tests\unit\mocks\repositories\MockProductPriceVersionRepository;
use tests\unit\mocks\repositories\MockProductRepository;
use tests\unit\mocks\repositories\MockTransactionManager;

/**
 * Unit tests for UpdateProductCommercialConditionsUseCase.
 */
class UpdateProductCommercialConditionsUseCaseTest extends TestCase
{
	private MockProductRepository $product_repository;
	private MockProductPriceVersionRepository $price_version_repository;
	private MockCourseRepository $course_repository;
	private MockCategoryRepository $category_repository;
	private MockPaymentConfigurationRepository $payment_configuration_repository;
	private MockCostCenterRepository $cost_center_repository;
	private MockTransactionManager $transaction_manager;
	private UpdateProductCommercialConditionsUseCase $use_case;
	private Course $course;
	private Product $product;
	private PaymentConfiguration $payment_config;
	private CostCenter $cost_center;

	protected function setUp(): void
	{
		parent::setUp();
		$this->product_repository = new MockProductRepository();
		$this->price_version_repository = new MockProductPriceVersionRepository();
		$this->course_repository = new MockCourseRepository();
		$this->category_repository = new MockCategoryRepository();
		$this->payment_configuration_repository = new MockPaymentConfigurationRepository();
		$this->cost_center_repository = new MockCostCenterRepository();
		$this->transaction_manager = new MockTransactionManager();

		$this->use_case = new UpdateProductCommercialConditionsUseCase(
			$this->product_repository,
			$this->price_version_repository,
			$this->course_repository,
			$this->transaction_manager,
			$this->payment_configuration_repository,
			$this->cost_center_repository
		);

		$category = $this->category_repository->create(Category::create('Negócios', 'negocios'));
		$this->course = $this->course_repository->create(Course::create($category->get_id(), 'Gestão de Projetos', 'gestao-projetos'));

		$this->payment_config = $this->payment_configuration_repository->save(
			PaymentConfiguration::create('Config Asaas', 1, ['credit_card', 'pix'])
		);

		$this->cost_center = $this->cost_center_repository->save(
			CostCenter::create('Centro Padrão', 'Descrição padrão')
		);

		// Initial product at R$ 100,00
		$this->product = $this->product_repository->save(Product::create(
			$this->course->get_id(),
			false,
			Price::from_cents(10000),
			BillingModel::one_time(),
			InstallmentLimit::from_int(3),
			null,
			null,
			new EnabledPaymentMethods(['pix', 'credit_card']),
			$this->payment_config->get_id(),
			$this->cost_center->get_id()
		));

		// Seed initial version 1
		$this->price_version_repository->save(ProductPriceVersion::create(
			$this->product->get_id(),
			1,
			null,
			Price::from_cents(10000),
			['price_in_cents' => 10000],
			'Initial version',
			1
		));
	}

	public function test_update_commercial_conditions_with_effective_price_change_creates_version(): void
	{
		$updated = $this->use_case->execute(
			$this->product->get_id(),
			false, // is_free
			15000, // new price: R$ 150,00
			BillingModel::ONE_TIME,
			5,
			null,
			null,
			['pix', 'credit_card'],
			$this->payment_config->get_id(),
			$this->cost_center->get_id(),
			'Aumento anual de tabela',
			99 // user id
		);

		$this->assertFalse($updated->is_free());
		$this->assertSame(15000, $updated->get_price()->get_cents());
		$this->assertSame(5, $updated->get_max_installments()->get_limit());

		// Verify version 2 was created
		$latest_version = $this->price_version_repository->find_latest_by_product_id($this->product->get_id());
		$this->assertNotNull($latest_version);
		$this->assertSame(2, $latest_version->get_version_number());
		$this->assertSame(10000, $latest_version->get_previous_price()->get_cents());
		$this->assertSame(15000, $latest_version->get_new_price()->get_cents());
		$this->assertSame('Aumento anual de tabela', $latest_version->get_change_reason());
		$this->assertSame(99, $latest_version->get_created_by());

		$snapshot = $latest_version->get_snapshot();
		$this->assertSame(15000, $snapshot['price_in_cents']);
		$this->assertSame(5, $snapshot['max_installments']);
	}

	public function test_update_commercial_conditions_without_price_change_does_not_create_version(): void
	{
		$versions_before = count($this->price_version_repository->list_by_product_id($this->product->get_id()));

		$updated = $this->use_case->execute(
			$this->product->get_id(),
			false, // is_free
			10000, // same price R$ 100,00
			BillingModel::ONE_TIME,
			10, // changed installments
			null,
			null,
			['credit_card'], // changed methods
			$this->payment_config->get_id(),
			$this->cost_center->get_id()
		);

		$this->assertSame(10000, $updated->get_price()->get_cents());
		$this->assertSame(10, $updated->get_max_installments()->get_limit());

		$versions_after = count($this->price_version_repository->list_by_product_id($this->product->get_id()));
		$this->assertSame($versions_before, $versions_after);
	}

	public function test_consecutive_price_changes_generate_sequential_versions(): void
	{
		// First price change to R$ 120,00
		$this->use_case->execute(
			$this->product->get_id(),
			false,
			12000,
			BillingModel::ONE_TIME,
			3,
			null,
			null,
			['pix']
		);

		// Second price change to R$ 140,00
		$this->use_case->execute(
			$this->product->get_id(),
			false,
			14000,
			BillingModel::ONE_TIME,
			3,
			null,
			null,
			['pix']
		);

		$versions = $this->price_version_repository->list_by_product_id($this->product->get_id());
		$this->assertCount(3, $versions);
		$this->assertSame(3, $versions[0]->get_version_number());
		$this->assertSame(14000, $versions[0]->get_new_price()->get_cents());
		$this->assertSame(12000, $versions[0]->get_previous_price()->get_cents());

		$this->assertSame(2, $versions[1]->get_version_number());
		$this->assertSame(12000, $versions[1]->get_new_price()->get_cents());
		$this->assertSame(10000, $versions[1]->get_previous_price()->get_cents());
	}

	public function test_update_commercial_conditions_switching_paid_to_free_creates_version(): void
	{
		$updated = $this->use_case->execute(
			$this->product->get_id(),
			true, // is_free
			null, // price null
			BillingModel::ONE_TIME,
			1,
			null,
			null,
			[],
			null,
			null,
			'Tornando o curso gratuito'
		);

		$this->assertTrue($updated->is_free());
		$this->assertNull($updated->get_price());

		$latest_version = $this->price_version_repository->find_latest_by_product_id($this->product->get_id());
		$this->assertNotNull($latest_version);
		$this->assertTrue($latest_version->is_free());
		$this->assertSame(10000, $latest_version->get_previous_price()->get_cents());
		$this->assertNull($latest_version->get_new_price());
	}

	public function test_update_non_existing_product_throws_not_found(): void
	{
		$this->expectException(NotFoundException::class);
		$this->use_case->execute(9999, false, 10000);
	}

	public function test_update_with_inactive_payment_configuration_throws_validation_exception(): void
	{
		$inactive_config = $this->payment_configuration_repository->save(
			PaymentConfiguration::create('Config Inativa', 1, ['pix'], null, false)
		);

		$this->expectException(ValidationException::class);
		$this->use_case->execute(
			$this->product->get_id(),
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

	public function test_update_with_inactive_cost_center_throws_validation_exception(): void
	{
		$inactive_cc = $this->cost_center_repository->save(
			CostCenter::create('Centro Inativo', null, false)
		);

		$this->expectException(ValidationException::class);
		$this->use_case->execute(
			$this->product->get_id(),
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
