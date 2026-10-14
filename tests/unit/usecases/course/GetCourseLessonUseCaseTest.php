<?php

namespace tests\unit\usecases\course;

use app\domain\course\CourseLesson;
use app\domain\course\LessonBlock;
use app\domain\exceptions\NotFoundException;
use app\usecases\course\GetCourseLessonUseCase;
use PHPUnit\Framework\TestCase;
use tests\unit\mocks\repositories\MockCourseLessonRepository;
use tests\unit\mocks\repositories\MockLessonBlockRepository;

/**
 * Unit tests for GetCourseLessonUseCase.
 */
class GetCourseLessonUseCaseTest extends TestCase
{
	private MockCourseLessonRepository $lesson_repository;
	private MockLessonBlockRepository $block_repository;
	private GetCourseLessonUseCase $use_case;
	private CourseLesson $lesson;

	protected function setUp(): void
	{
		parent::setUp();
		$this->lesson_repository = new MockCourseLessonRepository();
		$this->block_repository = new MockLessonBlockRepository();
		$this->use_case = new GetCourseLessonUseCase($this->lesson_repository, $this->block_repository);

		$this->lesson = $this->lesson_repository->create(CourseLesson::create(
			1,
			'Aula com Blocos',
			'Descrição',
			1
		));

		$this->block_repository->create(LessonBlock::create(
			(int) $this->lesson->get_id(),
			'video',
			['url' => 'https://example.com/video.mp4', 'duration_in_seconds' => 500],
			1
		));
	}

	public function test_get_lesson_success(): void
	{
		$found = $this->use_case->execute((int) $this->lesson->get_id());

		$this->assertSame((int) $this->lesson->get_id(), $found->get_id());
		$this->assertSame('Aula com Blocos', $found->get_title());
		$this->assertCount(1, $found->get_blocks());
		$this->assertSame('video', $found->get_blocks()[0]->get_type());
	}

	public function test_get_lesson_not_found_throws_exception(): void
	{
		$this->expectException(NotFoundException::class);
		$this->use_case->execute(999);
	}
}
