<?php

namespace app\usecases\course;

use app\domain\course\constants\ContentStatus;
use app\domain\course\CourseModule;
use app\domain\course\repositories\CourseModuleRepositoryInterface;
use app\domain\course\repositories\CourseRepositoryInterface;
use app\domain\exceptions\NotFoundException;

/**
 * Use case for creating a course module.
 */
class CreateCourseModuleUseCase
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
	 * Execute module creation.
	 *
	 * @param int $course_id
	 * @param string $title
	 * @param string|null $description
	 * @param int|null $sort_order
	 * @param string $status
	 * @return CourseModule
	 * @throws NotFoundException If parent course does not exist
	 */
	public function execute(
		int $course_id,
		string $title,
		?string $description = null,
		?int $sort_order = null,
		string $status = ContentStatus::DRAFT
	): CourseModule
	{
		$course = $this->course_repository->find_by_id($course_id);
		if ($course === null) {
			throw new NotFoundException("Course not found");
		}

		if ($sort_order === null || $sort_order <= 0) {
			$sort_order = $this->module_repository->get_next_sort_order($course_id);
		}

		$module = CourseModule::create(
			$course_id,
			$title,
			$description,
			$sort_order,
			$status
		);

		return $this->module_repository->create($module);
	}
}
