<?php

namespace app\usecases\course;

use app\domain\course\CourseModule;
use app\domain\course\repositories\CourseModuleRepositoryInterface;
use app\domain\exceptions\NotFoundException;

/**
 * Use case for retrieving details of a course module.
 */
class GetCourseModuleUseCase
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
	 * Execute module retrieval.
	 *
	 * @param int $module_id
	 * @return CourseModule
	 * @throws NotFoundException If module not found
	 */
	public function execute(int $module_id): CourseModule
	{
		$module = $this->module_repository->find_by_id($module_id);
		if ($module === null) {
			throw new NotFoundException("Module not found");
		}

		return $module;
	}
}
