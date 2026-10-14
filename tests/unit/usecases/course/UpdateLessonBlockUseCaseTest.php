<?php

namespace tests\unit\usecases\course;

use app\domain\course\CourseLesson;
use app\domain\course\LessonBlock;
use app\domain\exceptions\NotFoundException;
use app\usecases\course\UpdateLessonBlockUseCase;
use PHPUnit\Framework\TestCase;
use tests\unit\mocks\repositories\MockLessonBlockRepository;

/**
 * Unit tests for UpdateLessonBlockUseCase.
 */
class UpdateLessonBlockUseCaseTest extends TestCase
{
	private MockLessonBlockRepository $block_repository;
	private UpdateLessonBlockUseCase $use_case;
	private LessonBlock $block;

	protected function setUp(): void
	{
		parent::setUp();
		$this->block_repository = new MockLessonBlockRepository();
		$this->use_case = new UpdateLessonBlockUseCase($this->block_repository);

		$this->block = $this->block_repository->create(LessonBlock::create(
			1,
			'text',
			['content' => 'Conteúdo antigo'],
			1
		));
	}

	/**
	 * Test updating a block successfully.
	 *
	 * @return void
	 */
	public function test_update_block_success(): void
	{
		$updated = $this->use_case->execute(
			(int) $this->block->get_id(),
			'text',
			['content' => 'Conteúdo atualizado com sucesso!', 'min_reading_time_in_minutes' => 4],
			3,
			false
		);

		$this->assertSame('text', $updated->get_type());
		$this->assertSame(['content' => 'Conteúdo atualizado com sucesso!', 'min_reading_time_in_minutes' => 4], $updated->get_payload());
		$this->assertSame(3, $updated->get_sort_order());
		$this->assertFalse($updated->is_required());
	}

	/**
	 * Test updating a block that is not found throws exception.
	 *
	 * @return void
	 */
	public function test_update_block_not_found_throws_exception(): void
	{
		$this->expectException(NotFoundException::class);
		$this->use_case->execute(999, 'text', ['content' => 'Algo']);
	}
}
