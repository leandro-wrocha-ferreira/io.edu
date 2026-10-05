<?php

namespace tests\unit\usecases\course;

use app\domain\course\Category;
use app\domain\course\Course;
use app\domain\course\value_objects\CourseAccessPeriod;
use app\domain\course\value_objects\CourseStatus;
use app\domain\exceptions\CategoryNotFoundException;
use app\domain\exceptions\CourseNotFoundException;
use app\domain\exceptions\DuplicateSlugException;
use app\usecases\course\UpdateCourseUseCase;
use PHPUnit\Framework\TestCase;
use tests\unit\mocks\repositories\MockCategoryRepository;
use tests\unit\mocks\repositories\MockCourseRepository;

/**
 * Unit tests for UpdateCourseUseCase.
 */
class UpdateCourseUseCaseTest extends TestCase
{
	private MockCourseRepository $course_repository;
	private MockCategoryRepository $category_repository;
	private UpdateCourseUseCase $use_case;
	private Category $category;

	protected function setUp(): void
	{
		parent::setUp();
		$this->course_repository = new MockCourseRepository();
		$this->category_repository = new MockCategoryRepository();
		$this->use_case = new UpdateCourseUseCase($this->course_repository, $this->category_repository);

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
			'https://example.com/new.png',
			'Novos objetivos',
			'Novo público',
			'Novos requisitos',
			false
		);

		$this->assertSame('Curso Atualizado', $updated->get_title());
		$this->assertSame('curso-atualizado', (string) $updated->get_slug());
		$this->assertTrue($updated->is_active());
		$this->assertSame(180, $updated->get_access_period()->get_days());
		$this->assertSame(40, $updated->get_workload_in_hours());
		$this->assertFalse($updated->is_certificate_enabled());
	}

	public function test_update_course_keeping_same_slug_success(): void
	{
		$course = $this->course_repository->create(Course::create(
			$this->category->get_id(),
			'Curso Original',
			'slug-mesmo'
		));

		$updated = $this->use_case->execute(
			$course->get_id(),
			$this->category->get_id(),
			'Curso com Novo Título',
			'slug-mesmo'
		);

		$this->assertSame('slug-mesmo', (string) $updated->get_slug());
		$this->assertSame('Curso com Novo Título', $updated->get_title());
	}

	public function test_update_non_existing_course_throws_exception(): void
	{
		$this->expectException(CourseNotFoundException::class);
		$this->use_case->execute(999, $this->category->get_id(), 'Título');
	}

	public function test_update_with_non_existing_category_throws_exception(): void
	{
		$course = $this->course_repository->create(Course::create(
			$this->category->get_id(),
			'Curso Original',
			'curso-original'
		));

		$this->expectException(CategoryNotFoundException::class);
		$this->use_case->execute($course->get_id(), 999, 'Título');
	}

	public function test_update_with_duplicate_slug_on_another_course_throws_exception(): void
	{
		$this->course_repository->create(Course::create(
			$this->category->get_id(),
			'Curso 1',
			'slug-1'
		));

		$course2 = $this->course_repository->create(Course::create(
			$this->category->get_id(),
			'Curso 2',
			'slug-2'
		));

		$this->expectException(DuplicateSlugException::class);
		$this->use_case->execute($course2->get_id(), $this->category->get_id(), 'Curso 2 Modificado', 'slug-1');
	}
}
