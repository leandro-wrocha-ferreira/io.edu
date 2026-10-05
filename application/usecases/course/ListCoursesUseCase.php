<?php

namespace app\usecases\course;

use app\domain\course\Course;
use app\domain\course\repositories\CourseRepositoryInterface;

/**
 * Use case for listing all courses.
 */
class ListCoursesUseCase
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
	 * Execute course list retrieval.
	 *
	 * @return array<Course>
	 */
	public function execute(): array
	{
		return $this->course_repository->find_all();
	}
}
