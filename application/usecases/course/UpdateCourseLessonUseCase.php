<?php

namespace app\usecases\course;

use app\domain\course\CourseLesson;
use app\domain\course\repositories\CourseLessonRepositoryInterface;
use app\domain\exceptions\NotFoundException;

/**
 * Use case for updating a course lesson.
 */
class UpdateCourseLessonUseCase
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
	 * Execute lesson update.
	 *
	 * @param int $lesson_id
	 * @param string $title
	 * @param string|null $description
	 * @param string|null $status
	 * @param int|null $sort_order
	 * @return CourseLesson
	 * @throws NotFoundException If lesson not found
	 */
	public function execute(
		int $lesson_id,
		string $title,
		?string $description = null,
		?string $status = null,
		?int $sort_order = null
	): CourseLesson
	{
		$lesson = $this->lesson_repository->find_by_id($lesson_id);
		if ($lesson === null) {
			throw new NotFoundException("Lesson not found");
		}

		$lesson->update($title, $description, $status);

		if ($sort_order !== null && $sort_order >= 0) {
			$lesson->change_sort_order($sort_order);
		}

		return $this->lesson_repository->save($lesson);
	}
}
