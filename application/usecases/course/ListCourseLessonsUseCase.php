<?php

namespace app\usecases\course;

use app\domain\course\CourseLesson;
use app\domain\course\repositories\CourseLessonRepositoryInterface;
use app\domain\course\repositories\CourseModuleRepositoryInterface;
use app\domain\exceptions\NotFoundException;

/**
 * Use case for listing lessons belonging to a module.
 */
class ListCourseLessonsUseCase
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
	 * Execute lesson listing.
	 *
	 * @param int $module_id
	 * @param bool $only_published
	 * @return array<CourseLesson>
	 * @throws NotFoundException If module not found
	 */
	public function execute(int $module_id, bool $only_published = false): array
	{
		$module = $this->module_repository->find_by_id($module_id);
		if ($module === null) {
			throw new NotFoundException("Module not found");
		}

		return $this->lesson_repository->find_by_module_id($module_id, $only_published);
	}
}
