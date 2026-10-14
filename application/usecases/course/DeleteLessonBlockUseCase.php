<?php

namespace app\usecases\course;

use app\domain\course\LessonBlock;
use app\domain\course\repositories\LessonBlockRepositoryInterface;
use app\domain\exceptions\NotFoundException;

/**
 * Use case for deleting a lesson content block.
 */
class DeleteLessonBlockUseCase
{
	/** @var LessonBlockRepositoryInterface */
	private LessonBlockRepositoryInterface $block_repository;

	/**
	 * Constructor.
	 *
	 * @param LessonBlockRepositoryInterface $block_repository
	 */
	public function __construct(LessonBlockRepositoryInterface $block_repository)
	{
		$this->block_repository = $block_repository;
	}

	/**
	 * Execute block deletion.
	 *
	 * @param int $block_id
	 * @return LessonBlock
	 * @throws NotFoundException If block not found
	 */
	public function execute(int $block_id): LessonBlock
	{
		$block = $this->block_repository->find_by_id($block_id);
		if ($block === null) {
			throw new NotFoundException("Lesson block not found");
		}

		return $this->block_repository->delete($block);
	}
}
