<?php

namespace tests\unit\usecases\course;

use app\domain\commercial\value_objects\BillingModel;
use app\domain\course\Category;
use app\domain\course\value_objects\CourseAccessPeriod;
use app\domain\course\value_objects\CourseStatus;
use app\domain\exceptions\ConflictException;
use app\domain\exceptions\NotFoundException;
use app\domain\exceptions\ValidationException;
use app\usecases\course\CreateCourseUseCase;
use PHPUnit\Framework\TestCase;
use tests\unit\mocks\repositories\MockCategoryRepository;
use tests\unit\mocks\repositories\MockCourseRepository;
use tests\unit\mocks\repositories\MockProductPriceVersionRepository;
use tests\unit\mocks\repositories\MockProductRepository;
use tests\unit\mocks\repositories\MockTransactionManager;

/**
 * Unit tests for CreateCourseUseCase.
 */
class CreateCourseUseCaseTest extends TestCase
{
	private MockCourseRepository $course_repository;
	private MockCategoryRepository $category_repository;
	private MockProductRepository $product_repository;
	private MockProductPriceVersionRepository $price_version_repository;
	private MockTransactionManager $transaction_manager;
	private CreateCourseUseCase $use_case;
	private Category $category;

	protected function setUp(): void
	{
		parent::setUp();
		$this->course_repository = new MockCourseRepository();
		$this->category_repository = new MockCategoryRepository();
		$this->product_repository = new MockProductRepository();
		$this->price_version_repository = new MockProductPriceVersionRepository();
		$this->transaction_manager = new MockTransactionManager();

		$this->use_case = new CreateCourseUseCase(
			$this->course_repository,
			$this->category_repository,
			$this->product_repository,
			$this->transaction_manager,
			$this->price_version_repository
		);

		$this->category = $this->category_repository->create(Category::create('Programação', 'programacao'));
	}

	public function test_create_course_success_with_limited_time_access(): void
	{
		$course = $this->use_case->execute(
			$this->category->get_id(),
			'Curso de PHP 8',
			'curso-php-8',
			CourseStatus::DRAFT,
			CourseAccessPeriod::TYPE_LIMITED_TIME,
			365,
			80,
			'Resumo do curso',
			'Descrição detalhada',
			'public/uploads/courses/1/image.png',
			1800,
			'Objetivos',
			'Público',
			'Requisitos',
			true,
			'https://example.com/shared.jpg'
		);

		$this->assertNotNull($course->get_id());
		$this->assertSame($this->category->get_id(), $course->get_category_id());
		$this->assertSame('Curso de PHP 8', $course->get_title());
		$this->assertSame('curso-php-8', (string) $course->get_slug());
		$this->assertTrue($course->is_draft());
		$this->assertTrue($course->get_access_period()->is_limited_time());
		$this->assertSame(365, $course->get_access_period()->get_days());
		$this->assertSame(80, $course->get_workload_in_hours());
		$this->assertSame('public/uploads/courses/1/image.png', $course->get_image());
		$this->assertSame('https://example.com/shared.jpg', $course->get_image_url());
		$this->assertTrue($course->is_certificate_enabled());
		$this->assertSame(80, $course->get_video_min_completion_percentage());

		// Verify 1:1 Product was atomically created
		$product = $this->product_repository->find_by_course_id($course->get_id());
		$this->assertNotNull($product);
		$this->assertTrue($product->is_free());
		$this->assertNull($product->get_price());
		$this->assertTrue($this->transaction_manager->was_committed());
	}

	public function test_create_course_success_with_lifetime_access(): void
	{
		$course = $this->use_case->execute(
			$this->category->get_id(),
			'Curso Vitalício',
			null,
			CourseStatus::ACTIVE,
			CourseAccessPeriod::TYPE_LIFETIME,
			null
		);

		$this->assertTrue($course->is_active());
		$this->assertTrue($course->get_access_period()->is_lifetime());
		$this->assertNull($course->get_access_period()->get_days());
		$this->assertSame('curso-vitalicio', (string) $course->get_slug());

		$product = $this->product_repository->find_by_course_id($course->get_id());
		$this->assertNotNull($product);
		$this->assertTrue($product->is_free());
		$this->assertNull($product->get_price());
	}

	public function test_create_course_with_commercial_paid_product(): void
	{
		$course = $this->use_case->execute(
			$this->category->get_id(),
			'Curso Pago',
			'curso-pago',
			CourseStatus::ACTIVE,
			CourseAccessPeriod::TYPE_LIFETIME,
			null,
			40,
			'Resumo',
			'Desc',
			null,
			0,
			null,
			null,
			null,
			true,
			null,
			80,
			true, // is_available_for_sale
			false, // is_free
			19900, // R$ 199,00
			BillingModel::ONE_TIME,
			12,
			null,
			null,
			['credit_card', 'pix'],
			1, // payment_configuration_id
			2  // cost_center_id
		);

		$product = $this->product_repository->find_by_course_id($course->get_id());
		$this->assertNotNull($product);
		$this->assertFalse($product->is_free());
		$this->assertSame(19900, $product->get_price()->get_cents());
		$this->assertSame(12, $product->get_max_installments()->get_limit());
		$this->assertTrue($product->get_payment_methods()->has('credit_card'));
		$this->assertSame(1, $product->get_payment_configuration_id());
		$this->assertSame(2, $product->get_cost_center_id());

		// Verify version 1 was created
		$latest_version = $this->price_version_repository->find_latest_by_product_id($product->get_id());
		$this->assertNotNull($latest_version);
		$this->assertSame(1, $latest_version->get_version_number());
		$this->assertSame(19900, $latest_version->get_new_price()->get_cents());
		$this->assertNull($latest_version->get_previous_price());
	}

	public function test_create_course_with_inactive_status(): void
	{
		$course = $this->use_case->execute(
			$this->category->get_id(),
			'Curso Inativo',
			'curso-inativo',
			CourseStatus::INACTIVE
		);

		$this->assertTrue($course->is_inactive());
		$this->assertFalse($course->is_active());
		$this->assertFalse($course->is_draft());
	}

	public function test_create_course_with_non_existing_category_throws_exception(): void
	{
		$this->expectException(NotFoundException::class);
		$this->use_case->execute(999, 'Título Sem Categoria');
	}

	public function test_create_course_with_duplicate_slug_throws_conflict_exception(): void
	{
		$this->use_case->execute($this->category->get_id(), 'Curso Original', 'slug-duplicado');

		$this->expectException(ConflictException::class);
		$this->use_case->execute($this->category->get_id(), 'Outro Curso', 'slug-duplicado');
	}

	public function test_create_course_with_sales_enabled_on_draft_throws_validation_exception(): void
	{
		$this->expectException(ValidationException::class);
		$this->use_case->execute(
			$this->category->get_id(),
			'Curso Rascunho',
			'curso-rascunho-venda',
			CourseStatus::DRAFT,
			CourseAccessPeriod::TYPE_LIFETIME,
			null,
			null,
			null,
			null,
			null,
			0,
			null,
			null,
			null,
			true,
			null,
			80,
			true // cannot be available for sale if draft
		);
	}

	public function test_create_course_with_id_placeholder_slug_replaces_with_course_id(): void
	{
		$course = $this->use_case->execute(
			$this->category->get_id(),
			'Curso Novo Teste',
			'[id]-curso-novo-teste'
		);

		$this->assertSame($course->get_id() . '-curso-novo-teste', (string) $course->get_slug());
	}

	public function test_create_course_with_literal_id_slug_replaces_with_course_id(): void
	{
		$course = $this->use_case->execute(
			$this->category->get_id(),
			'Outro Curso Teste',
			'id-outro-curso-teste'
		);

		$this->assertSame($course->get_id() . '-outro-curso-teste', (string) $course->get_slug());
	}
}
