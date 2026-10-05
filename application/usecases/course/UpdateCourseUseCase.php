<?php

namespace app\usecases\course;

use app\domain\course\Course;
use app\domain\course\repositories\CategoryRepositoryInterface;
use app\domain\course\repositories\CourseRepositoryInterface;
use app\domain\course\value_objects\CourseAccessPeriod;
use app\domain\course\value_objects\CourseSlug;
use app\domain\course\value_objects\CourseStatus;
use app\domain\course\value_objects\Workload;
use app\domain\exceptions\CategoryNotFoundException;
use app\domain\exceptions\CourseNotFoundException;
use app\domain\exceptions\DuplicateSlugException;

/**
 * Use case for updating an existing course.
 */
class UpdateCourseUseCase
{
	/** @var CourseRepositoryInterface */
	private CourseRepositoryInterface $course_repository;

	/** @var CategoryRepositoryInterface */
	private CategoryRepositoryInterface $category_repository;

	/**
	 * Constructor.
	 *
	 * @param CourseRepositoryInterface $course_repository
	 * @param CategoryRepositoryInterface $category_repository
	 */
	public function __construct(
		CourseRepositoryInterface $course_repository,
		CategoryRepositoryInterface $category_repository
	)
	{
		$this->course_repository = $course_repository;
		$this->category_repository = $category_repository;
	}

	/**
	 * Execute course update.
	 *
	 * @param int $id Course ID
	 * @param int $category_id Category ID
	 * @param string $title Course title
	 * @param string|null $slug Course slug (auto-generated if null)
	 * @param string $status Status ('draft', 'active', 'archived')
	 * @param string $access_period_type Access period type ('lifetime' or 'limited_time')
	 * @param int|null $access_days Access days (required if limited_time)
	 * @param int|null $workload_in_hours Workload in hours
	 * @param string|null $short_description Short description
	 * @param string|null $description Full description
	 * @param string|null $image Cover image URL/path
	 * @param string|null $objectives Learning objectives
	 * @param string|null $target_audience Target audience
	 * @param string|null $requirements Prerequisites
	 * @param bool $certificate_enabled Certificate emission toggle
	 * @return Course
	 * @throws CourseNotFoundException When course not found
	 * @throws CategoryNotFoundException When category not found
	 * @throws DuplicateSlugException When slug is already taken
	 */
	public function execute(
		int $id,
		int $category_id,
		string $title,
		?string $slug = null,
		string $status = CourseStatus::DRAFT,
		string $access_period_type = CourseAccessPeriod::TYPE_LIMITED_TIME,
		?int $access_days = 365,
		?int $workload_in_hours = null,
		?string $short_description = null,
		?string $description = null,
		?string $image = null,
		?string $objectives = null,
		?string $target_audience = null,
		?string $requirements = null,
		bool $certificate_enabled = true
	): Course
	{
		$course = $this->course_repository->find_by_id($id);
		if ($course === null) {
			throw new CourseNotFoundException("Curso com ID {$id} não foi encontrado.");
		}

		$category = $this->category_repository->find_by_id($category_id);
		if ($category === null) {
			throw new CategoryNotFoundException("Categoria informada (ID {$category_id}) não foi encontrada.");
		}

		$course_slug = !empty($slug) ? new CourseSlug($slug) : CourseSlug::from_title($title);

		$existing = $this->course_repository->find_by_slug($course_slug);
		if ($existing !== null && $existing->get_id() !== $id) {
			throw new DuplicateSlugException("Já existe outro curso com o slug '{$course_slug}'.");
		}

		$access_period = new CourseAccessPeriod($access_period_type, $access_days);
		$workload = $workload_in_hours !== null ? new Workload($workload_in_hours) : null;

		$course->update_details(
			$category_id,
			$title,
			$course_slug,
			$access_period,
			$workload,
			$short_description,
			$description,
			$image,
			$objectives,
			$target_audience,
			$requirements,
			$certificate_enabled
		);

		$target_status = new CourseStatus($status);
		if ($target_status->is_active()) {
			$course->publish();
		} elseif ($target_status->is_archived()) {
			$course->archive();
		} else {
			$course->draft();
		}

		return $this->course_repository->save($course);
	}
}
