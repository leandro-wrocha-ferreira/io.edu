<?php

namespace app\usecases\course;

use app\domain\course\CourseLesson;
use app\domain\course\repositories\CourseLessonRepositoryInterface;
use app\domain\course\repositories\LessonBlockRepositoryInterface;
use app\domain\exceptions\NotFoundException;

/**
 * Use case for retrieving details of a course lesson along with its content blocks.
 */
class GetCourseLessonUseCase
{
	/** @var CourseLessonRepositoryInterface */
	private CourseLessonRepositoryInterface $lesson_repository;

	/** @var LessonBlockRepositoryInterface */
	private LessonBlockRepositoryInterface $block_repository;

	/**
	 * Constructor.
	 *
	 * @param CourseLessonRepositoryInterface $lesson_repository
	 * @param LessonBlockRepositoryInterface $block_repository
	 */
	public function __construct(
		CourseLessonRepositoryInterface $lesson_repository,
		LessonBlockRepositoryInterface $block_repository
	)
	{
		$this->lesson_repository = $lesson_repository;
		$this->block_repository = $block_repository;
	}

	/**
	 * Execute lesson retrieval.
	 *
	 * @param int $lesson_id
	 * @return CourseLesson
	 * @throws NotFoundException If lesson not found
	 */
	public function execute(int $lesson_id): CourseLesson
	{
		$lesson = $this->lesson_repository->find_by_id($lesson_id);
		if ($lesson === null) {
			throw new NotFoundException("Lesson not found");
		}

		$blocks = $this->block_repository->find_by_lesson_id($lesson_id);
		$lesson->set_blocks($blocks);

		return $lesson;
	}
}
