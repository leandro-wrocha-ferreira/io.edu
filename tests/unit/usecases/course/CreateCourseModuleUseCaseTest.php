<?php

namespace tests\unit\usecases\course;

use app\domain\course\Course;
use app\domain\course\CourseModule;
use app\domain\course\value_objects\CourseAccessPeriod;
use app\domain\course\value_objects\CourseSlug;
use app\domain\course\value_objects\CourseStatus;
use app\domain\exceptions\NotFoundException;
use app\usecases\course\CreateCourseModuleUseCase;
use PHPUnit\Framework\TestCase;
use tests\unit\mocks\repositories\MockCourseModuleRepository;
use tests\unit\mocks\repositories\MockCourseRepository;

/**
 * Unit tests for CreateCourseModuleUseCase.
 */
class CreateCourseModuleUseCaseTest extends TestCase
{
	private MockCourseRepository $course_repository;
	private MockCourseModuleRepository $module_repository;
	private CreateCourseModuleUseCase $use_case;
	private Course $course;

	protected function setUp(): void
	{
		parent::setUp();
		$this->course_repository = new MockCourseRepository();
		$this->module_repository = new MockCourseModuleRepository();
		$this->use_case = new CreateCourseModuleUseCase($this->module_repository, $this->course_repository);

		$this->course = $this->course_repository->create(Course::create(
			1,
			'PHP Avançado',
			new CourseSlug('php-avancado'),
			new CourseStatus(CourseStatus::DRAFT),
			new CourseAccessPeriod(CourseAccessPeriod::TYPE_LIFETIME)
		));
	}

	public function test_create_module_success(): void
	{
		$module = $this->use_case->execute(
			(int) $this->course->get_id(),
			'Módulo 1: Fundamentos',
			'Introdução à arquitetura',
			1,
			'draft'
		);

		$this->assertNotNull($module->get_id());
		$this->assertSame((int) $this->course->get_id(), $module->get_course_id());
		$this->assertSame('Módulo 1: Fundamentos', $module->get_title());
		$this->assertSame('Introdução à arquitetura', $module->get_description());
		$this->assertSame(1, $module->get_sort_order());
		$this->assertTrue($module->is_draft());
	}

	public function test_create_module_auto_assigns_sort_order(): void
	{
		$module_one = $this->use_case->execute(
			(int) $this->course->get_id(),
			'Primeiro Módulo'
		);

		$module_two = $this->use_case->execute(
			(int) $this->course->get_id(),
			'Segundo Módulo'
		);

		$this->assertSame(1, $module_one->get_sort_order());
		$this->assertSame(2, $module_two->get_sort_order());
	}

	public function test_create_module_with_non_existing_course_throws_exception(): void
	{
		$this->expectException(NotFoundException::class);
		$this->use_case->execute(999, 'Módulo Sem Curso');
	}

	public function test_create_module_allows_same_title_in_same_course(): void
	{
		$module_one = $this->use_case->execute((int) $this->course->get_id(), 'Módulo Duplicado');
		$module_two = $this->use_case->execute((int) $this->course->get_id(), 'Módulo Duplicado');

		$this->assertNotSame($module_one->get_id(), $module_two->get_id());
		$this->assertSame('Módulo Duplicado', $module_one->get_title());
		$this->assertSame('Módulo Duplicado', $module_two->get_title());
	}
}
