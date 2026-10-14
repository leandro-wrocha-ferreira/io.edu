<?php

namespace tests\unit\usecases\course;

use app\domain\course\Course;
use app\domain\course\CourseModule;
use app\domain\course\value_objects\CourseAccessPeriod;
use app\domain\course\value_objects\CourseSlug;
use app\domain\course\value_objects\CourseStatus;
use app\domain\exceptions\NotFoundException;
use app\usecases\course\ReorderCourseModulesUseCase;
use PHPUnit\Framework\TestCase;
use tests\unit\mocks\repositories\MockCourseModuleRepository;
use tests\unit\mocks\repositories\MockCourseRepository;

/**
 * Unit tests for ReorderCourseModulesUseCase.
 */
class ReorderCourseModulesUseCaseTest extends TestCase
{
	private MockCourseRepository $course_repository;
	private MockCourseModuleRepository $module_repository;
	private ReorderCourseModulesUseCase $use_case;
	private Course $course;
	private CourseModule $module_a;
	private CourseModule $module_b;

	protected function setUp(): void
	{
		parent::setUp();
		$this->course_repository = new MockCourseRepository();
		$this->module_repository = new MockCourseModuleRepository();
		$this->use_case = new ReorderCourseModulesUseCase($this->module_repository, $this->course_repository);

		$this->course = $this->course_repository->create(Course::create(
			1,
			'Curso Reorder',
			new CourseSlug('curso-reorder'),
			new CourseStatus(CourseStatus::ACTIVE),
			new CourseAccessPeriod(CourseAccessPeriod::TYPE_LIFETIME)
		));

		$this->module_a = $this->module_repository->create(CourseModule::create(
			(int) $this->course->get_id(),
			'Módulo A',
			null,
			1
		));

		$this->module_b = $this->module_repository->create(CourseModule::create(
			(int) $this->course->get_id(),
			'Módulo B',
			null,
			2
		));
	}

	public function test_reorder_modules_success(): void
	{
		$this->use_case->execute((int) $this->course->get_id(), [
			(int) $this->module_b->get_id(),
			(int) $this->module_a->get_id(),
		]);

		$modules = $this->module_repository->find_by_course_id((int) $this->course->get_id());
		$this->assertSame((int) $this->module_b->get_id(), $modules[0]->get_id());
		$this->assertSame((int) $this->module_a->get_id(), $modules[1]->get_id());
	}

	public function test_reorder_modules_with_non_existing_course_throws_exception(): void
	{
		$this->expectException(NotFoundException::class);
		$this->use_case->execute(999, [1, 2]);
	}
}
