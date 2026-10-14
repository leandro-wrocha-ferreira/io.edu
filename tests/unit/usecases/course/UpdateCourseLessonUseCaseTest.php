<?php

namespace tests\unit\usecases\course;

use app\domain\course\CourseLesson;
use app\domain\exceptions\NotFoundException;
use app\usecases\course\UpdateCourseLessonUseCase;
use PHPUnit\Framework\TestCase;
use tests\unit\mocks\repositories\MockCourseLessonRepository;

/**
 * Unit tests for UpdateCourseLessonUseCase.
 */
class UpdateCourseLessonUseCaseTest extends TestCase
{
	private MockCourseLessonRepository $lesson_repository;
	private UpdateCourseLessonUseCase $use_case;
	private CourseLesson $lesson;

	protected function setUp(): void
	{
		parent::setUp();
		$this->lesson_repository = new MockCourseLessonRepository();
		$this->use_case = new UpdateCourseLessonUseCase($this->lesson_repository);

		$this->lesson = $this->lesson_repository->create(CourseLesson::create(
			1,
			'Aula Antiga',
			'Descrição antiga',
			1,
			'draft'
		));
	}

	public function test_update_lesson_success(): void
	{
		$updated = $this->use_case->execute(
			(int) $this->lesson->get_id(),
			'Aula Renovada',
			'Nova descrição',
			'published',
			4
		);

		$this->assertSame('Aula Renovada', $updated->get_title());
		$this->assertSame('Nova descrição', $updated->get_description());
		$this->assertTrue($updated->is_published());
		$this->assertSame(4, $updated->get_sort_order());
	}

	public function test_update_lesson_not_found_throws_exception(): void
	{
		$this->expectException(NotFoundException::class);
		$this->use_case->execute(999, 'Título');
	}
}
