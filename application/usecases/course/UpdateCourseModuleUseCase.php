<?php

namespace app\usecases\course;

use app\domain\course\CourseModule;
use app\domain\course\repositories\CourseModuleRepositoryInterface;
use app\domain\exceptions\NotFoundException;

/**
 * Use case for updating an existing course module.
 */
class UpdateCourseModuleUseCase
{
	/** @var CourseModuleRepositoryInterface */
	private CourseModuleRepositoryInterface $module_repository;

	/**
	 * Constructor.
	 *
	 * @param CourseModuleRepositoryInterface $module_repository
	 */
	public function __construct(CourseModuleRepositoryInterface $module_repository)
	{
		$this->module_repository = $module_repository;
	}

	/**
	 * Execute module update.
	 *
	 * @param int $module_id
	 * @param string $title
	 * @param string|null $description
	 * @param string|null $status
	 * @param int|null $sort_order
	 * @return CourseModule
	 * @throws NotFoundException If module not found
	 */
	public function execute(
		int $module_id,
		string $title,
		?string $description = null,
		?string $status = null,
		?int $sort_order = null
	): CourseModule
	{
		$module = $this->module_repository->find_by_id($module_id);
		if ($module === null) {
			throw new NotFoundException("Module not found");
		}

		$module->update($title, $description, $status);

		if ($sort_order !== null && $sort_order >= 0) {
			$module->change_sort_order($sort_order);
		}

		return $this->module_repository->save($module);
	}
}
