<?php

namespace app\usecases\course;

use app\domain\course\Course;
use app\domain\course\repositories\CourseRepositoryInterface;
use app\domain\exceptions\NotFoundException;

/**
 * Use case for archiving a course.
 */
class ArchiveCourseUseCase
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
	 * Execute course archival.
	 *
	 * @param int $id Course ID
	 * @return Course
	 * @throws NotFoundException When course not found
	 */
	public function execute(int $id): Course
	{
		$course = $this->course_repository->find_by_id($id);
		if ($course === null) {
			throw new NotFoundException("Course not found");
		}

		$course->archive();
		return $this->course_repository->save($course);
	}
}
