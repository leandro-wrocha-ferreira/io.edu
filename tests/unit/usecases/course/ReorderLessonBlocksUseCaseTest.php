<?php

namespace tests\unit\usecases\course;

use app\domain\course\CourseLesson;
use app\domain\course\LessonBlock;
use app\domain\exceptions\NotFoundException;
use app\usecases\course\ReorderLessonBlocksUseCase;
use PHPUnit\Framework\TestCase;
use tests\unit\mocks\repositories\MockCourseLessonRepository;
use tests\unit\mocks\repositories\MockLessonBlockRepository;

/**
 * Unit tests for ReorderLessonBlocksUseCase.
 */
class ReorderLessonBlocksUseCaseTest extends TestCase
{
	private MockCourseLessonRepository $lesson_repository;
	private MockLessonBlockRepository $block_repository;
	private ReorderLessonBlocksUseCase $use_case;
	private CourseLesson $lesson;
	private LessonBlock $block_a;
	private LessonBlock $block_b;

	protected function setUp(): void
	{
		parent::setUp();
		$this->lesson_repository = new MockCourseLessonRepository();
		$this->block_repository = new MockLessonBlockRepository();
		$this->use_case = new ReorderLessonBlocksUseCase($this->block_repository, $this->lesson_repository);

		$this->lesson = $this->lesson_repository->create(CourseLesson::create(
			1,
			'Aula Reorder Blocks'
		));

		$this->block_a = $this->block_repository->create(LessonBlock::create(
			(int) $this->lesson->get_id(),
			'text',
			['content' => 'Bloco A'],
			1
		));

		$this->block_b = $this->block_repository->create(LessonBlock::create(
			(int) $this->lesson->get_id(),
			'quiz',
			['question' => 'Pergunta do Bloco B'],
			2
		));
	}

	public function test_reorder_blocks_success(): void
	{
		$this->use_case->execute((int) $this->lesson->get_id(), [
			(int) $this->block_b->get_id(),
			(int) $this->block_a->get_id(),
		]);

		$blocks = $this->block_repository->find_by_lesson_id((int) $this->lesson->get_id());
		$this->assertSame((int) $this->block_b->get_id(), $blocks[0]->get_id());
		$this->assertSame((int) $this->block_a->get_id(), $blocks[1]->get_id());
	}

	public function test_reorder_blocks_with_non_existing_lesson_throws_exception(): void
	{
		$this->expectException(NotFoundException::class);
		$this->use_case->execute(999, [1, 2]);
	}
}
