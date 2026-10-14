<?php

namespace tests\unit\usecases\course;

use app\domain\course\LessonBlock;
use app\domain\exceptions\NotFoundException;
use app\usecases\course\GetLessonBlockUseCase;
use PHPUnit\Framework\TestCase;
use tests\unit\mocks\repositories\MockLessonBlockRepository;

/**
 * Unit tests for GetLessonBlockUseCase.
 */
class GetLessonBlockUseCaseTest extends TestCase
{
	private MockLessonBlockRepository $block_repository;
	private GetLessonBlockUseCase $use_case;
	private LessonBlock $block;

	protected function setUp(): void
	{
		parent::setUp();
		$this->block_repository = new MockLessonBlockRepository();
		$this->use_case = new GetLessonBlockUseCase($this->block_repository);

		$this->block = $this->block_repository->create(LessonBlock::create(
			1,
			'video',
			['provider' => 'youtube', 'url' => 'https://youtube.com/watch?v=123'],
			1
		));
	}

	public function test_get_block_success(): void
	{
		$found = $this->use_case->execute((int) $this->block->get_id());

		$this->assertSame((int) $this->block->get_id(), $found->get_id());
		$this->assertSame('video', $found->get_type());
		$this->assertSame('youtube', $found->get_payload()['provider']);
	}

	public function test_get_block_not_found_throws_exception(): void
	{
		$this->expectException(NotFoundException::class);
		$this->use_case->execute(999);
	}
}
