<?php

namespace app\usecases\course;

use app\domain\course\repositories\CourseLessonRepositoryInterface;
use app\domain\course\repositories\CourseModuleRepositoryInterface;
use app\domain\exceptions\NotFoundException;

/**
 * Use case for reordering lessons within a module.
 */
class ReorderCourseLessonsUseCase
{
	/** @var CourseLessonRepositoryInterface */
	private CourseLessonRepositoryInterface $lesson_repository;

	/** @var CourseModuleRepositoryInterface */
	private CourseModuleRepositoryInterface $module_repository;

	/**
	 * Constructor.
	 *
	 * @param CourseLessonRepositoryInterface $lesson_repository
	 * @param CourseModuleRepositoryInterface $module_repository
	 */
	public function __construct(
		CourseLessonRepositoryInterface $lesson_repository,
		CourseModuleRepositoryInterface $module_repository
	)
	{
		$this->lesson_repository = $lesson_repository;
		$this->module_repository = $module_repository;
	}

	/**
	 * Execute lessons reordering.
	 *
	 * @param int $module_id
	 * @param array<int> $lesson_ids Ordered list of lesson IDs
	 * @return void
	 * @throws NotFoundException If module not found
	 */
	public function execute(int $module_id, array $lesson_ids): void
	{
		$module = $this->module_repository->find_by_id($module_id);
		if ($module === null) {
			throw new NotFoundException("Module not found");
		}

		$this->lesson_repository->reorder($module_id, $lesson_ids);
	}
}
