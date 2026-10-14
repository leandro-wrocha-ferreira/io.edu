<?php

namespace app\usecases\course;

use app\domain\course\CourseLesson;
use app\domain\course\repositories\CourseLessonRepositoryInterface;
use app\domain\exceptions\NotFoundException;

/**
 * Use case for deleting a course lesson.
 */
class DeleteCourseLessonUseCase
{
	/** @var CourseLessonRepositoryInterface */
	private CourseLessonRepositoryInterface $lesson_repository;

	/**
	 * Constructor.
	 *
	 * @param CourseLessonRepositoryInterface $lesson_repository
	 */
	public function __construct(CourseLessonRepositoryInterface $lesson_repository)
	{
		$this->lesson_repository = $lesson_repository;
	}

	/**
	 * Execute lesson deletion.
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

		return $this->lesson_repository->delete($lesson);
	}
}
