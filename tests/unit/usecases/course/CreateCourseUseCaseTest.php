<?php

namespace tests\unit\usecases\course;

use app\domain\course\Category;
use app\domain\course\value_objects\CourseAccessPeriod;
use app\domain\course\value_objects\CourseStatus;
use app\domain\exceptions\ConflictException;
use app\domain\exceptions\NotFoundException;
use app\usecases\course\CreateCourseUseCase;
use PHPUnit\Framework\TestCase;
use tests\unit\mocks\repositories\MockCategoryRepository;
use tests\unit\mocks\repositories\MockCourseRepository;

/**
 * Unit tests for CreateCourseUseCase.
 */
class CreateCourseUseCaseTest extends TestCase
{
	private MockCourseRepository $course_repository;
	private MockCategoryRepository $category_repository;
	private CreateCourseUseCase $use_case;
	private Category $category;

	protected function setUp(): void
	{
		parent::setUp();
		$this->course_repository = new MockCourseRepository();
		$this->category_repository = new MockCategoryRepository();
		$this->use_case = new CreateCourseUseCase($this->course_repository, $this->category_repository);

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
}
