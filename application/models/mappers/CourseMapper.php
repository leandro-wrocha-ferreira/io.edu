<?php

namespace app\models\mappers;

use app\domain\course\Course;
use app\domain\course\value_objects\CourseAccessPeriod;
use app\domain\course\value_objects\CourseSlug;
use app\domain\course\value_objects\CourseStatus;
use app\domain\course\value_objects\Workload;
use app\models\dtos\CourseDatabase;

/**
 * Mapper for converting between raw database data and Course domain entity.
 */
class CourseMapper
{
	/**
	 * Convert a raw database row DTO into a Course domain entity.
	 *
	 * @param CourseDatabase $row Raw database row DTO
	 * @return Course Course domain entity
	 */
	public static function to_entity(CourseDatabase $row): Course
	{
		$access_type = $row->access_period_type;
		$access_days = $row->access_days;

		if ($access_type === CourseAccessPeriod::TYPE_LIMITED_TIME && ($access_days === null || $access_days <= 0)) {
			$access_type = CourseAccessPeriod::TYPE_LIFETIME;
			$access_days = null;
		}

		$access_period = new CourseAccessPeriod($access_type, $access_days);
		$workload = $row->workload_in_hours !== null ? new Workload($row->workload_in_hours) : null;

		return Course::create(
			$row->category_id,
			$row->title,
			new CourseSlug($row->slug),
			new CourseStatus($row->status),
			$access_period,
			$workload,
			$row->short_description,
			$row->description,
			$row->image,
			$row->duration_in_seconds,
			$row->objectives,
			$row->target_audience,
			$row->requirements,
			(bool) $row->certificate_enabled,
			$row->id,
			$row->created_at,
			$row->updated_at,
			$row->deleted_at,
			$row->category_name
		);
	}

	/**
	 * Convert multiple raw database rows into an array of Course domain entities.
	 *
	 * @param array<array<string, mixed>> $rows
	 * @return array<Course>
	 */
	public static function to_entities(array $rows): array
	{
		if (empty($rows)) {
			return [];
		}

		$entities = [];
		foreach ($rows as $row) {
			$entities[] = self::to_entity(new CourseDatabase($row));
		}

		return $entities;
	}

	/**
	 * Map a Course entity to a database array for record creation.
	 *
	 * @param Course $course Course entity
	 * @return array<string, mixed> Raw database columns map
	 */
	public static function to_database_create(Course $course): array
	{
		return [
			'category_id' => $course->get_category_id(),
			'title' => $course->get_title(),
			'slug' => (string) $course->get_slug(),
			'short_description' => $course->get_short_description(),
			'description' => $course->get_description(),
			'image' => $course->get_image(),
			'status' => (string) $course->get_status(),
			'workload_in_hours' => $course->get_workload_in_hours(),
			'duration_in_seconds' => $course->get_duration_in_seconds(),
			'objectives' => $course->get_objectives(),
			'target_audience' => $course->get_target_audience(),
			'requirements' => $course->get_requirements(),
			'access_period_type' => $course->get_access_period()->get_type(),
			'access_days' => $course->get_access_period()->get_days(),
			'certificate_enabled' => $course->is_certificate_enabled() ? 1 : 0,
		];
	}

	/**
	 * Map a Course entity to a database array for record update.
	 *
	 * @param Course $course Course entity
	 * @return array<string, mixed> Raw database columns map
	 */
	public static function to_database_update(Course $course): array
	{
		return [
			'category_id' => $course->get_category_id(),
			'title' => $course->get_title(),
			'slug' => (string) $course->get_slug(),
			'short_description' => $course->get_short_description(),
			'description' => $course->get_description(),
			'image' => $course->get_image(),
			'status' => (string) $course->get_status(),
			'workload_in_hours' => $course->get_workload_in_hours(),
			'duration_in_seconds' => $course->get_duration_in_seconds(),
			'objectives' => $course->get_objectives(),
			'target_audience' => $course->get_target_audience(),
			'requirements' => $course->get_requirements(),
			'access_period_type' => $course->get_access_period()->get_type(),
			'access_days' => $course->get_access_period()->get_days(),
			'certificate_enabled' => $course->is_certificate_enabled() ? 1 : 0,
			'deleted_at' => $course->get_deleted_at() ? $course->get_deleted_at()->format('Y-m-d H:i:s') : null,
		];
	}
}
