<?php

namespace tests\unit\usecases\course;

use app\domain\course\LessonBlock;
use app\domain\exceptions\NotFoundException;
use app\usecases\course\DeleteLessonBlockUseCase;
use PHPUnit\Framework\TestCase;
use tests\unit\mocks\repositories\MockLessonBlockRepository;

/**
 * Unit tests for DeleteLessonBlockUseCase.
 */
class DeleteLessonBlockUseCaseTest extends TestCase
{
	private MockLessonBlockRepository $block_repository;
	private DeleteLessonBlockUseCase $use_case;
	private LessonBlock $block;

	protected function setUp(): void
	{
		parent::setUp();
		$this->block_repository = new MockLessonBlockRepository();
		$this->use_case = new DeleteLessonBlockUseCase($this->block_repository);

		$this->block = $this->block_repository->create(LessonBlock::create(
			1,
			'attachment',
			['filename' => 'guia.pdf', 'filepath' => 'uploads/guia.pdf'],
			1
		));
	}

	public function test_delete_block_success(): void
	{
		$deleted = $this->use_case->execute((int) $this->block->get_id());

		$this->assertTrue($deleted->is_deleted());
		$this->assertNull($this->block_repository->find_by_id((int) $this->block->get_id()));
	}

	public function test_delete_block_not_found_throws_exception(): void
	{
		$this->expectException(NotFoundException::class);
		$this->use_case->execute(999);
	}
}
