<?php

namespace tests\unit\usecases\course;

use app\domain\commercial\Product;
use app\domain\commercial\value_objects\BillingModel;
use app\domain\commercial\value_objects\EnabledPaymentMethods;
use app\domain\commercial\value_objects\InstallmentLimit;
use app\domain\commercial\value_objects\Price;
use app\domain\course\Category;
use app\domain\course\Course;
use app\domain\course\value_objects\CourseAccessPeriod;
use app\domain\course\value_objects\CourseStatus;
use app\domain\exceptions\ConflictException;
use app\domain\exceptions\NotFoundException;
use app\domain\exceptions\ValidationException;
use app\usecases\course\UpdateCourseUseCase;
use PHPUnit\Framework\TestCase;
use tests\unit\mocks\repositories\MockCategoryRepository;
use tests\unit\mocks\repositories\MockCourseRepository;
use tests\unit\mocks\repositories\MockProductRepository;

/**
 * Unit tests for UpdateCourseUseCase.
 */
class UpdateCourseUseCaseTest extends TestCase
{
	private MockCourseRepository $course_repository;
	private MockCategoryRepository $category_repository;
	private MockProductRepository $product_repository;
	private UpdateCourseUseCase $use_case;
	private Category $category;

	protected function setUp(): void
	{
		parent::setUp();
		$this->course_repository = new MockCourseRepository();
		$this->category_repository = new MockCategoryRepository();
		$this->product_repository = new MockProductRepository();
		$this->use_case = new UpdateCourseUseCase(
			$this->course_repository,
			$this->category_repository,
			$this->product_repository
		);

		$this->category = $this->category_repository->create(Category::create('Programação', 'programacao'));
	}

	public function test_update_course_success(): void
	{
		$course = $this->course_repository->create(Course::create(
			$this->category->get_id(),
			'Curso Original',
			'curso-original'
		));

		$updated = $this->use_case->execute(
			$course->get_id(),
			$this->category->get_id(),
			'Curso Atualizado',
			'curso-atualizado',
			CourseStatus::ACTIVE,
			CourseAccessPeriod::TYPE_LIMITED_TIME,
			180,
			40,
			'Novo resumo',
			'Nova ementa',
			'public/uploads/courses/1/image.png',
			'Novos objetivos',
			'Novo público',
			'Novo requisitos',
			false,
			'https://example.com/shared-updated.jpg',
			75
		);

		$this->assertSame('Curso Atualizado', $updated->get_title());
		$this->assertSame('curso-atualizado', (string) $updated->get_slug());
		$this->assertTrue($updated->is_active());
		$this->assertSame(180, $updated->get_access_period()->get_days());
		$this->assertSame(40, $updated->get_workload_in_hours());
		$this->assertFalse($updated->is_certificate_enabled());
		$this->assertSame('public/uploads/courses/1/image.png', $updated->get_image());
		$this->assertSame('https://example.com/shared-updated.jpg', $updated->get_image_url());
		$this->assertSame(75, $updated->get_video_min_completion_percentage());
	}

	public function test_update_course_keeping_same_slug_success(): void
	{
		$course = $this->course_repository->create(Course::create(
			$this->category->get_id(),
			'Curso Original',
			'curso-original'
		));

		$updated = $this->use_case->execute(
			$course->get_id(),
			$this->category->get_id(),
			'Curso Original',
			'curso-original'
		);

		$this->assertSame('curso-original', (string) $updated->get_slug());
		$this->assertSame('Curso Original', $updated->get_title());
	}

	public function test_update_course_to_inactive_status(): void
	{
		$course = $this->course_repository->create(Course::create(
			$this->category->get_id(),
			'Curso Ativo',
			'curso-ativo',
			CourseStatus::ACTIVE
		));

		$updated = $this->use_case->execute(
			$course->get_id(),
			$this->category->get_id(),
			'Curso Ativo',
			'curso-ativo',
			CourseStatus::INACTIVE
		);

		$this->assertTrue($updated->is_inactive());
		$this->assertFalse($updated->is_active());
	}

	public function test_update_course_transition_back_to_draft_throws_validation_exception(): void
	{
		$course = $this->course_repository->create(Course::create(
			$this->category->get_id(),
			'Curso Publicado',
			'curso-publicado',
			CourseStatus::ACTIVE
		));

		$this->expectException(ValidationException::class);
		$this->use_case->execute(
			$course->get_id(),
			$this->category->get_id(),
			'Curso Publicado',
			'curso-publicado',
			CourseStatus::DRAFT
		);
	}

	public function test_update_course_enables_sale_when_active_and_valid(): void
	{
		$course = $this->course_repository->create(Course::create(
			$this->category->get_id(),
			'Curso Gratuito Ativo',
			'curso-gratuito-ativo',
			CourseStatus::ACTIVE
		));

		$this->product_repository->save(Product::create(
			$course->get_id(),
			true,
			null
		));

		$updated = $this->use_case->execute(
			$course->get_id(),
			$this->category->get_id(),
			'Curso Gratuito Ativo',
			'curso-gratuito-ativo',
			CourseStatus::ACTIVE,
			CourseAccessPeriod::TYPE_LIFETIME,
			null,
			null,
			null,
			null,
			null,
			null,
			null,
			null,
			true,
			null,
			80,
			true // enable for sale
		);

		$this->assertTrue($updated->is_available_for_sale());
	}

	public function test_update_course_deactivating_clears_available_for_sale(): void
	{
		$course = $this->course_repository->create(Course::create(
			$this->category->get_id(),
			'Curso Ativo Venda',
			'curso-ativo-venda',
			CourseStatus::ACTIVE,
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
			null,
			null,
			null,
			null,
			null,
			80,
			true // available for sale
		));

		$updated = $this->use_case->execute(
			$course->get_id(),
			$this->category->get_id(),
			'Curso Ativo Venda',
			'curso-ativo-venda',
			CourseStatus::INACTIVE
		);

		$this->assertFalse($updated->is_available_for_sale());
	}

	public function test_update_course_enabling_sale_on_paid_product_without_config_throws_exception(): void
	{
		$course = $this->course_repository->create(Course::create(
			$this->category->get_id(),
			'Curso Incompleto',
			'curso-incompleto',
			CourseStatus::ACTIVE
		));

		$this->product_repository->save(Product::create(
			$course->get_id(),
			false,
			Price::from_cents(9900),
			BillingModel::one_time(),
			InstallmentLimit::single(),
			null,
			null,
			new EnabledPaymentMethods([]) // missing payment methods & config
		));

		$this->expectException(ValidationException::class);
		$this->use_case->execute(
			$course->get_id(),
			$this->category->get_id(),
			'Curso Incompleto',
			'curso-incompleto',
			CourseStatus::ACTIVE,
			CourseAccessPeriod::TYPE_LIFETIME,
			null,
			null,
			null,
			null,
			null,
			null,
			null,
			null,
			true,
			null,
			80,
			true // cannot enable sale for incomplete paid product
		);
	}

	public function test_update_non_existing_course_throws_exception(): void
	{
		$this->expectException(NotFoundException::class);
		$this->use_case->execute(999, $this->category->get_id(), 'Título');
	}

	public function test_update_with_non_existing_category_throws_exception(): void
	{
		$course = $this->course_repository->create(Course::create(
			$this->category->get_id(),
			'Curso Original',
			'curso-original'
		));

		$this->expectException(NotFoundException::class);
		$this->use_case->execute($course->get_id(), 999, 'Título');
	}

	public function test_update_with_duplicate_slug_on_another_course_throws_exception(): void
	{
		$this->course_repository->create(Course::create(
			$this->category->get_id(),
			'Curso 1',
			'curso-1'
		));

		$course2 = $this->course_repository->create(Course::create(
			$this->category->get_id(),
			'Curso 2',
			'curso-2'
		));

		$this->expectException(ConflictException::class);
		$this->use_case->execute($course2->get_id(), $this->category->get_id(), 'Curso 2 Modificado', 'curso-1');
	}

	public function test_update_course_with_id_placeholder_slug_formats_with_course_id(): void
	{
		$course = $this->course_repository->create(Course::create(
			$this->category->get_id(),
			'Curso Antigo',
			'id-curso-antigo'
		));

		$updated = $this->use_case->execute(
			$course->get_id(),
			$this->category->get_id(),
			'Curso Antigo',
			'id-curso-antigo'
		);

		$this->assertSame($course->get_id() . '-curso-antigo', (string) $updated->get_slug());
	}
}
