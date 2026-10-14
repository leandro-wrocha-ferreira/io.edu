<?php

namespace app\usecases\course;

use app\domain\course\LessonBlock;
use app\domain\course\repositories\CourseLessonRepositoryInterface;
use app\domain\course\repositories\LessonBlockRepositoryInterface;
use app\domain\exceptions\NotFoundException;

/**
 * Use case for listing all blocks belonging to a lesson.
 */
class ListLessonBlocksUseCase
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
	 * Execute block listing.
	 *
	 * @param int $lesson_id
	 * @return array<LessonBlock>
	 * @throws NotFoundException If lesson not found
	 */
	public function execute(int $lesson_id): array
	{
		$lesson = $this->lesson_repository->find_by_id($lesson_id);
		if ($lesson === null) {
			throw new NotFoundException("Lesson not found");
		}

		return $this->block_repository->find_by_lesson_id($lesson_id);
	}
}
