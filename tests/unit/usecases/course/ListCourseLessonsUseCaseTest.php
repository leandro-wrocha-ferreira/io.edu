<?php

namespace tests\unit\usecases\course;

use app\domain\course\CourseLesson;
use app\domain\course\CourseModule;
use app\domain\exceptions\NotFoundException;
use app\usecases\course\ListCourseLessonsUseCase;
use PHPUnit\Framework\TestCase;
use tests\unit\mocks\repositories\MockCourseLessonRepository;
use tests\unit\mocks\repositories\MockCourseModuleRepository;

/**
 * Unit tests for ListCourseLessonsUseCase.
 */
class ListCourseLessonsUseCaseTest extends TestCase
{
	private MockCourseModuleRepository $module_repository;
	private MockCourseLessonRepository $lesson_repository;
	private ListCourseLessonsUseCase $use_case;
	private CourseModule $module;

	protected function setUp(): void
	{
		parent::setUp();
		$this->module_repository = new MockCourseModuleRepository();
		$this->lesson_repository = new MockCourseLessonRepository();
		$this->use_case = new ListCourseLessonsUseCase($this->lesson_repository, $this->module_repository);

		$this->module = $this->module_repository->create(CourseModule::create(
			1,
			'Módulo Test List Lessons'
		));

		$this->lesson_repository->create(CourseLesson::create(
			(int) $this->module->get_id(),
			'Aula 1',
			null,
			1,
			'published'
		));

		$this->lesson_repository->create(CourseLesson::create(
			(int) $this->module->get_id(),
			'Aula 2 (Draft)',
			null,
			2,
			'draft'
		));
	}

	public function test_list_all_lessons_success(): void
	{
		$lessons = $this->use_case->execute((int) $this->module->get_id());

		$this->assertCount(2, $lessons);
	}

	public function test_list_only_published_lessons_success(): void
	{
		$lessons = $this->use_case->execute((int) $this->module->get_id(), true);

		$this->assertCount(1, $lessons);
		$this->assertSame('Aula 1', $lessons[0]->get_title());
	}

	public function test_list_lessons_with_non_existing_module_throws_exception(): void
	{
		$this->expectException(NotFoundException::class);
		$this->use_case->execute(999);
	}
}
