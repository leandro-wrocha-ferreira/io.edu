<?php

namespace app\usecases\course;

use app\domain\course\Course;
use app\domain\course\repositories\CategoryRepositoryInterface;
use app\domain\course\repositories\CourseRepositoryInterface;
use app\domain\course\value_objects\CourseAccessPeriod;
use app\domain\course\value_objects\CourseSlug;
use app\domain\course\value_objects\CourseStatus;
use app\domain\course\value_objects\Workload;
use app\domain\exceptions\ConflictException;
use app\domain\exceptions\NotFoundException;

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
	 * @param string $status Status ('draft', 'active', 'inactive', 'archived')
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
	 * @param string|null $image_url External image URL
	 * @return Course
	 * @throws NotFoundException When course or category not found
	 * @throws ConflictException When slug is already taken
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
		bool $certificate_enabled = true,
		?string $image_url = null
	): Course
	{
		$course = $this->course_repository->find_by_id($id);
		if ($course === null) {
			throw new NotFoundException("Course not found");
		}

		$category = $this->category_repository->find_by_id($category_id);
		if ($category === null) {
			throw new NotFoundException("Category not found");
		}

		$course_slug = !empty($slug) ? new CourseSlug($slug) : new CourseSlug($title);

		$existing = $this->course_repository->find_by_slug($course_slug);
		if ($existing !== null && $existing->get_id() !== $id) {
			throw new ConflictException("Course slug already exists");
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
			$certificate_enabled,
			$image_url
		);

		if ($course->get_status()->get_value() !== $status) {
			$course->change_status($status);
		}

		return $this->course_repository->save($course);
	}
}
