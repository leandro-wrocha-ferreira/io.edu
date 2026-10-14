<?php

namespace app\models\mappers;

use app\domain\course\CourseModule;
use app\models\dtos\CourseModuleDatabase;

/**
 * Mapper for converting between raw database data and CourseModule domain entity.
 */
class CourseModuleMapper
{
	/**
	 * Convert a raw database row DTO into a CourseModule domain entity.
	 *
	 * @param CourseModuleDatabase $row Raw database row DTO
	 * @return CourseModule CourseModule domain entity
	 */
	public static function to_entity(CourseModuleDatabase $row): CourseModule
	{
		return CourseModule::create(
			$row->course_id,
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
	 * Convert multiple raw database rows into an array of CourseModule domain entities.
	 *
	 * @param array<array<string, mixed>> $rows
	 * @return array<CourseModule>
	 */
	public static function to_entities(array $rows): array
	{
		if (empty($rows)) {
			return [];
		}

		$entities = [];
		foreach ($rows as $row) {
			$entities[] = self::to_entity(new CourseModuleDatabase($row));
		}

		return $entities;
	}

	/**
	 * Map a CourseModule entity to a database array for record creation.
	 *
	 * @param CourseModule $module
	 * @return array<string, mixed>
	 */
	public static function to_database_create(CourseModule $module): array
	{
		return [
			'course_id' => $module->get_course_id(),
			'title' => $module->get_title(),
			'description' => $module->get_description(),
			'sort_order' => $module->get_sort_order(),
			'status' => $module->get_status(),
		];
	}

	/**
	 * Map a CourseModule entity to a database array for record update.
	 *
	 * @param CourseModule $module
	 * @return array<string, mixed>
	 */
	public static function to_database_update(CourseModule $module): array
	{
		return [
			'course_id' => $module->get_course_id(),
			'title' => $module->get_title(),
			'description' => $module->get_description(),
			'sort_order' => $module->get_sort_order(),
			'status' => $module->get_status(),
			'deleted_at' => $module->get_deleted_at() ? $module->get_deleted_at()->format('Y-m-d H:i:s') : null,
		];
	}
}
