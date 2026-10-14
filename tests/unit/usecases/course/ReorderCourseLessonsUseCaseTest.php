<?php

namespace tests\unit\usecases\course;

use app\domain\course\CourseLesson;
use app\domain\course\CourseModule;
use app\domain\exceptions\NotFoundException;
use app\usecases\course\ReorderCourseLessonsUseCase;
use PHPUnit\Framework\TestCase;
use tests\unit\mocks\repositories\MockCourseLessonRepository;
use tests\unit\mocks\repositories\MockCourseModuleRepository;

/**
 * Unit tests for ReorderCourseLessonsUseCase.
 */
class ReorderCourseLessonsUseCaseTest extends TestCase
{
	private MockCourseModuleRepository $module_repository;
	private MockCourseLessonRepository $lesson_repository;
	private ReorderCourseLessonsUseCase $use_case;
	private CourseModule $module;
	private CourseLesson $lesson_a;
	private CourseLesson $lesson_b;

	protected function setUp(): void
	{
		parent::setUp();
		$this->module_repository = new MockCourseModuleRepository();
		$this->lesson_repository = new MockCourseLessonRepository();
		$this->use_case = new ReorderCourseLessonsUseCase($this->lesson_repository, $this->module_repository);

		$this->module = $this->module_repository->create(CourseModule::create(
			1,
			'Módulo Reorder Lessons'
		));

		$this->lesson_a = $this->lesson_repository->create(CourseLesson::create(
			(int) $this->module->get_id(),
			'Aula A',
			null,
			1
		));

		$this->lesson_b = $this->lesson_repository->create(CourseLesson::create(
			(int) $this->module->get_id(),
			'Aula B',
			null,
			2
		));
	}

	public function test_reorder_lessons_success(): void
	{
		$this->use_case->execute((int) $this->module->get_id(), [
			(int) $this->lesson_b->get_id(),
			(int) $this->lesson_a->get_id(),
		]);

		$lessons = $this->lesson_repository->find_by_module_id((int) $this->module->get_id());
		$this->assertSame((int) $this->lesson_b->get_id(), $lessons[0]->get_id());
		$this->assertSame((int) $this->lesson_a->get_id(), $lessons[1]->get_id());
	}

	public function test_reorder_lessons_with_non_existing_module_throws_exception(): void
	{
		$this->expectException(NotFoundException::class);
		$this->use_case->execute(999, [1, 2]);
	}
}
