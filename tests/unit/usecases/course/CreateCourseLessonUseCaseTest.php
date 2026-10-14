<?php

namespace tests\unit\usecases\course;

use app\domain\course\CourseModule;
use app\domain\exceptions\NotFoundException;
use app\usecases\course\CreateCourseLessonUseCase;
use PHPUnit\Framework\TestCase;
use tests\unit\mocks\repositories\MockCourseLessonRepository;
use tests\unit\mocks\repositories\MockCourseModuleRepository;

/**
 * Unit tests for CreateCourseLessonUseCase.
 */
class CreateCourseLessonUseCaseTest extends TestCase
{
	private MockCourseModuleRepository $module_repository;
	private MockCourseLessonRepository $lesson_repository;
	private CreateCourseLessonUseCase $use_case;
	private CourseModule $module;

	protected function setUp(): void
	{
		parent::setUp();
		$this->module_repository = new MockCourseModuleRepository();
		$this->lesson_repository = new MockCourseLessonRepository();
		$this->use_case = new CreateCourseLessonUseCase($this->lesson_repository, $this->module_repository);

		$this->module = $this->module_repository->create(CourseModule::create(
			1,
			'Módulo 1: Setup',
			null,
			1
		));
	}

	public function test_create_lesson_success(): void
	{
		$lesson = $this->use_case->execute(
			(int) $this->module->get_id(),
			'Aula 1: Introdução ao PHP 8',
			'Visão geral da linguagem',
			1,
			'draft'
		);

		$this->assertNotNull($lesson->get_id());
		$this->assertSame((int) $this->module->get_id(), $lesson->get_module_id());
		$this->assertSame('Aula 1: Introdução ao PHP 8', $lesson->get_title());
		$this->assertSame('Visão geral da linguagem', $lesson->get_description());
		$this->assertSame(1, $lesson->get_sort_order());
		$this->assertTrue($lesson->is_draft());
	}

	public function test_create_lesson_auto_order(): void
	{
		$lesson = $this->use_case->execute(
			(int) $this->module->get_id(),
			'Primeira Aula do Módulo'
		);

		$this->assertSame('Primeira Aula do Módulo', $lesson->get_title());
		$this->assertSame(1, $lesson->get_sort_order());
	}

	public function test_create_lesson_with_non_existing_module_throws_exception(): void
	{
		$this->expectException(NotFoundException::class);
		$this->use_case->execute(999, 'Aula Sem Módulo');
	}
}
