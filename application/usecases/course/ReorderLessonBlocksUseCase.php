<?php

namespace app\usecases\course;

use app\domain\course\repositories\CourseLessonRepositoryInterface;
use app\domain\course\repositories\LessonBlockRepositoryInterface;
use app\domain\exceptions\NotFoundException;

/**
 * Use case for reordering content blocks within a lesson.
 */
class ReorderLessonBlocksUseCase
{
	/** @var LessonBlockRepositoryInterface */
	private LessonBlockRepositoryInterface $block_repository;

	/** @var CourseLessonRepositoryInterface */
	private CourseLessonRepositoryInterface $lesson_repository;

	/**
	 * Constructor.
	 *
	 * @param LessonBlockRepositoryInterface $block_repository
	 * @param CourseLessonRepositoryInterface $lesson_repository
	 */
	public function __construct(
		LessonBlockRepositoryInterface $block_repository,
		CourseLessonRepositoryInterface $lesson_repository
	)
	{
		$this->block_repository = $block_repository;
		$this->lesson_repository = $lesson_repository;
	}

	/**
	 * Execute blocks reordering.
	 *
	 * @param int $lesson_id
	 * @param array<int> $block_ids Ordered list of block IDs
	 * @return void
	 * @throws NotFoundException If lesson not found
	 */
	public function execute(int $lesson_id, array $block_ids): void
	{
		$lesson = $this->lesson_repository->find_by_id($lesson_id);
		if ($lesson === null) {
			throw new NotFoundException("Lesson not found");
		}

		$this->block_repository->reorder($lesson_id, $block_ids);
	}
}
