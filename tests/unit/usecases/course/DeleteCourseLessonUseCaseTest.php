<?php

namespace tests\unit\usecases\course;

use app\domain\course\CourseLesson;
use app\domain\exceptions\NotFoundException;
use app\usecases\course\DeleteCourseLessonUseCase;
use PHPUnit\Framework\TestCase;
use tests\unit\mocks\repositories\MockCourseLessonRepository;

/**
 * Unit tests for DeleteCourseLessonUseCase.
 */
class DeleteCourseLessonUseCaseTest extends TestCase
{
	private MockCourseLessonRepository $lesson_repository;
	private DeleteCourseLessonUseCase $use_case;
	private CourseLesson $lesson;

	protected function setUp(): void
	{
		parent::setUp();
		$this->lesson_repository = new MockCourseLessonRepository();
		$this->use_case = new DeleteCourseLessonUseCase($this->lesson_repository);

		$this->lesson = $this->lesson_repository->create(CourseLesson::create(
			1,
			'Aula para Deletar',
			null,
			1
		));
	}

	public function test_delete_lesson_success(): void
	{
		$deleted = $this->use_case->execute((int) $this->lesson->get_id());

		$this->assertTrue($deleted->is_deleted());
		$this->assertNull($this->lesson_repository->find_by_id((int) $this->lesson->get_id()));
	}

	public function test_delete_lesson_not_found_throws_exception(): void
	{
		$this->expectException(NotFoundException::class);
		$this->use_case->execute(999);
	}
}
