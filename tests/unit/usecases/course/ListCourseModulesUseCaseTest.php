<?php

namespace tests\unit\usecases\course;

use app\domain\course\Course;
use app\domain\course\CourseModule;
use app\domain\course\value_objects\CourseAccessPeriod;
use app\domain\course\value_objects\CourseSlug;
use app\domain\course\value_objects\CourseStatus;
use app\domain\exceptions\NotFoundException;
use app\usecases\course\ListCourseModulesUseCase;
use PHPUnit\Framework\TestCase;
use tests\unit\mocks\repositories\MockCourseModuleRepository;
use tests\unit\mocks\repositories\MockCourseRepository;

/**
 * Unit tests for ListCourseModulesUseCase.
 */
class ListCourseModulesUseCaseTest extends TestCase
{
	private MockCourseRepository $course_repository;
	private MockCourseModuleRepository $module_repository;
	private ListCourseModulesUseCase $use_case;
	private Course $course;

	protected function setUp(): void
	{
		parent::setUp();
		$this->course_repository = new MockCourseRepository();
		$this->module_repository = new MockCourseModuleRepository();
		$this->use_case = new ListCourseModulesUseCase($this->module_repository, $this->course_repository);

		$this->course = $this->course_repository->create(Course::create(
			1,
			'Curso List',
			new CourseSlug('curso-list'),
			new CourseStatus(CourseStatus::ACTIVE),
			new CourseAccessPeriod(CourseAccessPeriod::TYPE_LIFETIME)
		));

		$this->module_repository->create(CourseModule::create(
			(int) $this->course->get_id(),
			'Módulo 1',
			null,
			1,
			'published'
		));

		$this->module_repository->create(CourseModule::create(
			(int) $this->course->get_id(),
			'Módulo 2 (Draft)',
			null,
			2,
			'draft'
		));
	}

	public function test_list_all_modules_success(): void
	{
		$modules = $this->use_case->execute((int) $this->course->get_id());

		$this->assertCount(2, $modules);
	}

	public function test_list_only_published_modules_success(): void
	{
		$modules = $this->use_case->execute((int) $this->course->get_id(), true);

		$this->assertCount(1, $modules);
		$this->assertSame('Módulo 1', $modules[0]->get_title());
	}

	public function test_list_modules_with_non_existing_course_throws_exception(): void
	{
		$this->expectException(NotFoundException::class);
		$this->use_case->execute(999);
	}
}
