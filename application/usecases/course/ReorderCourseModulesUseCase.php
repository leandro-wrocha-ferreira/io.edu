<?php

namespace app\usecases\course;

use app\domain\course\repositories\CourseModuleRepositoryInterface;
use app\domain\course\repositories\CourseRepositoryInterface;
use app\domain\exceptions\NotFoundException;

/**
 * Use case for reordering modules within a course.
 */
class ReorderCourseModulesUseCase
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
	 * Execute modules reordering.
	 *
	 * @param int $course_id
	 * @param array<int> $module_ids Ordered list of module IDs
	 * @return void
	 * @throws NotFoundException If course not found
	 */
	public function execute(int $course_id, array $module_ids): void
	{
		$course = $this->course_repository->find_by_id($course_id);
		if ($course === null) {
			throw new NotFoundException("Course not found");
		}

		$this->module_repository->reorder($course_id, $module_ids);
	}
}
