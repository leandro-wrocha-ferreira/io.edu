<?php

namespace app\usecases\course;

use app\domain\course\constants\ContentStatus;
use app\domain\course\CourseLesson;
use app\domain\course\repositories\CourseLessonRepositoryInterface;
use app\domain\course\repositories\CourseModuleRepositoryInterface;
use app\domain\exceptions\NotFoundException;

/**
 * Use case for creating a course lesson within a module.
 */
class CreateCourseLessonUseCase
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
	 * Execute lesson creation.
	 *
	 * @param int $module_id
	 * @param string $title
	 * @param string|null $description
	 * @param int|null $sort_order
	 * @param string $status
	 * @return CourseLesson
	 * @throws NotFoundException If parent module not found
	 */
	public function execute(
		int $module_id,
		string $title,
		?string $description = null,
		?int $sort_order = null,
		string $status = ContentStatus::DRAFT
	): CourseLesson
	{
		$module = $this->module_repository->find_by_id($module_id);
		if ($module === null) {
			throw new NotFoundException("Module not found");
		}

		if ($sort_order === null || $sort_order <= 0) {
			$sort_order = $this->lesson_repository->get_next_sort_order($module_id);
		}

		$lesson = CourseLesson::create(
			$module_id,
			$title,
			$description,
			$sort_order,
			$status
		);

		return $this->lesson_repository->create($lesson);
	}
}
