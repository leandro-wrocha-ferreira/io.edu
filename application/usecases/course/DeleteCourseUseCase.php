<?php

namespace app\usecases\course;

use app\domain\course\Course;
use app\domain\course\repositories\CourseRepositoryInterface;
use app\domain\exceptions\CourseNotFoundException;

/**
 * Use case for deleting a course (soft delete).
 */
class DeleteCourseUseCase
{
	/** @var CourseRepositoryInterface */
	private CourseRepositoryInterface $course_repository;

	/**
	 * Constructor.
	 *
	 * @param CourseRepositoryInterface $course_repository
	 */
	public function __construct(CourseRepositoryInterface $course_repository)
	{
		$this->course_repository = $course_repository;
	}

	/**
	 * Execute course deletion.
	 *
	 * @param int $id Course ID
	 * @return Course
	 * @throws CourseNotFoundException When course not found
	 */
	public function execute(int $id): Course
	{
		$course = $this->course_repository->find_by_id($id);
		if ($course === null) {
			throw new CourseNotFoundException("Curso com ID {$id} não foi encontrado.");
		}

		return $this->course_repository->delete($course);
	}
}
