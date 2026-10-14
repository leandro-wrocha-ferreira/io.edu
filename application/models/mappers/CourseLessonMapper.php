<?php

namespace app\models\mappers;

use app\domain\course\CourseLesson;
use app\models\dtos\CourseLessonDatabase;

/**
 * Mapper for converting between raw database data and CourseLesson domain entity.
 */
class CourseLessonMapper
{
	/**
	 * Convert a raw database row DTO into a CourseLesson domain entity.
	 *
	 * @param CourseLessonDatabase $row Raw database row DTO
	 * @return CourseLesson CourseLesson domain entity
	 */
	public static function to_entity(CourseLessonDatabase $row): CourseLesson
	{
		return CourseLesson::create(
			$row->module_id,
			$row->title,
			$row->description,
			$row->sort_order,
			$row->status,
			$row->id,
			$row->created_at,
			$row->updated_at,
			$row->deleted_at
		);
	}

	/**
	 * Convert multiple raw database rows into an array of CourseLesson domain entities.
	 *
	 * @param array<array<string, mixed>> $rows
	 * @return array<CourseLesson>
	 */
	public static function to_entities(array $rows): array
	{
		if (empty($rows)) {
			return [];
		}

		$entities = [];
		foreach ($rows as $row) {
			$entities[] = self::to_entity(new CourseLessonDatabase($row));
		}

		return $entities;
	}

	/**
	 * Map a CourseLesson entity to a database array for record creation.
	 *
	 * @param CourseLesson $lesson
	 * @return array<string, mixed>
	 */
	public static function to_database_create(CourseLesson $lesson): array
	{
		return [
			'module_id' => $lesson->get_module_id(),
			'title' => $lesson->get_title(),
			'description' => $lesson->get_description(),
			'sort_order' => $lesson->get_sort_order(),
			'status' => $lesson->get_status(),
		];
	}

	/**
	 * Map a CourseLesson entity to a database array for record update.
	 *
	 * @param CourseLesson $lesson
	 * @return array<string, mixed>
	 */
	public static function to_database_update(CourseLesson $lesson): array
	{
		return [
			'module_id' => $lesson->get_module_id(),
			'title' => $lesson->get_title(),
			'description' => $lesson->get_description(),
			'sort_order' => $lesson->get_sort_order(),
			'status' => $lesson->get_status(),
			'deleted_at' => $lesson->get_deleted_at() ? $lesson->get_deleted_at()->format('Y-m-d H:i:s') : null,
		];
	}
}
