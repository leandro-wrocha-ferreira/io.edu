<?php

namespace tests\unit\usecases\course;

use app\domain\course\CourseLesson;
use app\domain\course\LessonBlock;
use app\domain\exceptions\NotFoundException;
use app\usecases\course\ListLessonBlocksUseCase;
use PHPUnit\Framework\TestCase;
use tests\unit\mocks\repositories\MockCourseLessonRepository;
use tests\unit\mocks\repositories\MockLessonBlockRepository;

/**
 * Unit tests for ListLessonBlocksUseCase.
 */
class ListLessonBlocksUseCaseTest extends TestCase
{
	private MockCourseLessonRepository $lesson_repository;
	private MockLessonBlockRepository $block_repository;
	private ListLessonBlocksUseCase $use_case;
	private CourseLesson $lesson;

	protected function setUp(): void
	{
		parent::setUp();
		$this->lesson_repository = new MockCourseLessonRepository();
		$this->block_repository = new MockLessonBlockRepository();
		$this->use_case = new ListLessonBlocksUseCase($this->block_repository, $this->lesson_repository);

		$this->lesson = $this->lesson_repository->create(CourseLesson::create(
			1,
			'Aula Test List Blocks'
		));

		$this->block_repository->create(LessonBlock::create(
			(int) $this->lesson->get_id(),
			'text',
			['content' => 'Bloco 1'],
			1
		));

		$this->block_repository->create(LessonBlock::create(
			(int) $this->lesson->get_id(),
			'attachment',
			['filename' => 'anexo.pdf', 'filepath' => 'uploads/anexo.pdf'],
			2
		));
	}

	public function test_list_blocks_success(): void
	{
		$blocks = $this->use_case->execute((int) $this->lesson->get_id());

		$this->assertCount(2, $blocks);
		$this->assertSame('text', $blocks[0]->get_type());
		$this->assertSame('attachment', $blocks[1]->get_type());
	}

	public function test_list_blocks_with_non_existing_lesson_throws_exception(): void
	{
		$this->expectException(NotFoundException::class);
		$this->use_case->execute(999);
	}
}
