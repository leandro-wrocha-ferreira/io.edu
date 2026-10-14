<?php

namespace app\usecases\course;

use app\domain\course\LessonBlock;
use app\domain\course\repositories\LessonBlockRepositoryInterface;
use app\domain\exceptions\NotFoundException;

/**
 * Use case for updating a lesson content block.
 */
class UpdateLessonBlockUseCase
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
	 * Execute block update.
	 *
	 * @param int $block_id
	 * @param string $type
	 * @param array<string, mixed>|string $payload
	 * @param int|null $sort_order
	 * @param bool $is_required
	 * @return LessonBlock
	 * @throws NotFoundException If block not found
	 */
	public function execute(
		int $block_id,
		string $type,
		array|string $payload,
		?int $sort_order = null,
		bool $is_required = true
	): LessonBlock
	{
		$block = $this->block_repository->find_by_id($block_id);
		if ($block === null) {
			throw new NotFoundException("Lesson block not found");
		}

		$order = ($sort_order !== null && $sort_order >= 0) ? $sort_order : $block->get_sort_order();

		$updated_block = LessonBlock::create(
			$block->get_lesson_id(),
			$type,
			$payload,
			$order,
			$is_required,
			$block->get_id(),
			$block->get_created_at()
		);

		return $this->block_repository->save($updated_block);
	}
}
