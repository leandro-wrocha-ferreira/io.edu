<?php

namespace app\usecases\course;

use app\domain\course\CourseModule;
use app\domain\course\repositories\CourseModuleRepositoryInterface;
use app\domain\course\repositories\CourseRepositoryInterface;
use app\domain\exceptions\NotFoundException;

/**
 * Use case for listing modules belonging to a course.
 */
class ListCourseModulesUseCase
{
	/** @var CourseModuleRepositoryInterface */
	private CourseModuleRepositoryInterface $module_repository;

	/** @var CourseRepositoryInterface */
	private CourseRepositoryInterface $course_repository;

	/**
	 * Constructor.
	 *
	 * @param CourseModuleRepositoryInterface $module_repository
	 * @param CourseRepositoryInterface $course_repository
	 */
	public function __construct(
		CourseModuleRepositoryInterface $module_repository,
		CourseRepositoryInterface $course_repository
	)
	{
		$this->module_repository = $module_repository;
		$this->course_repository = $course_repository;
	}

	/**
	 * Execute module listing.
	 *
	 * @param int $course_id
	 * @param bool $only_published
	 * @return array<CourseModule>
	 * @throws NotFoundException If course not found
	 */
	public function execute(int $course_id, bool $only_published = false): array
	{
		$course = $this->course_repository->find_by_id($course_id);
		if ($course === null) {
			throw new NotFoundException("Course not found");
		}

		return $this->module_repository->find_by_course_id($course_id, $only_published);
	}
}
