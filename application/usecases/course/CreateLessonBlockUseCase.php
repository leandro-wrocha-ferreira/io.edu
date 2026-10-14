<?php

namespace app\usecases\course;

use app\domain\course\LessonBlock;
use app\domain\course\repositories\CourseLessonRepositoryInterface;
use app\domain\course\repositories\LessonBlockRepositoryInterface;
use app\domain\exceptions\NotFoundException;

/**
 * Use case for creating a content block inside a lesson.
 */
class CreateLessonBlockUseCase
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
	 * Execute block creation.
	 *
	 * @param int $lesson_id
	 * @param string $type
	 * @param array<string, mixed>|string $payload
	 * @param int|null $sort_order
	 * @param bool $is_required
	 * @return LessonBlock
	 * @throws NotFoundException If parent lesson not found
	 */
	public function execute(
		int $lesson_id,
		string $type,
		array|string $payload = [],
		?int $sort_order = null,
		bool $is_required = true
	): LessonBlock
	{
		$lesson = $this->lesson_repository->find_by_id($lesson_id);
		if ($lesson === null) {
			throw new NotFoundException("Lesson not found");
		}

		if ($sort_order === null || $sort_order <= 0) {
			$sort_order = $this->block_repository->get_next_sort_order($lesson_id);
		}

		$block = LessonBlock::create(
			$lesson_id,
			$type,
			$payload,
			$sort_order,
			$is_required
		);

		return $this->block_repository->create($block);
	}
}
