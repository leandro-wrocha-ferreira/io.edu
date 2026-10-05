<?php

namespace app\models\mappers;

use app\domain\course\Category;
use app\models\dtos\CategoryDatabase;

/**
 * Mapper for converting between raw database data and Category domain entity.
 */
class CategoryMapper
{
	/**
	 * Convert a raw database row DTO into a Category domain entity.
	 *
	 * @param CategoryDatabase $row Raw database row DTO
	 * @return Category Category domain entity
	 */
	public static function to_entity(CategoryDatabase $row): Category
	{
		return Category::create(
			$row->name,
			$row->slug,
			$row->status,
			$row->id,
			$row->created_at,
			$row->updated_at,
			$row->deleted_at
		);
	}

	/**
	 * Convert multiple raw database rows into an array of Category domain entities.
	 *
	 * @param array<array<string, mixed>> $rows
	 * @return array<Category>
	 */
	public static function to_entities(array $rows): array
	{
		if (empty($rows)) {
			return [];
		}

		$entities = [];
		foreach ($rows as $row) {
			$entities[] = self::to_entity(new CategoryDatabase($row));
		}

		return $entities;
	}

	/**
	 * Map a Category entity to a database array for record creation.
	 *
	 * @param Category $category Category entity
	 * @return array<string, mixed> Raw database columns map
	 */
	public static function to_database_create(Category $category): array
	{
		return [
			'name' => $category->get_name(),
			'slug' => $category->get_slug(),
			'status' => $category->get_status(),
		];
	}

	/**
	 * Map a Category entity to a database array for record update.
	 *
	 * @param Category $category Category entity
	 * @return array<string, mixed> Raw database columns map
	 */
	public static function to_database_update(Category $category): array
	{
		return [
			'name' => $category->get_name(),
			'slug' => $category->get_slug(),
			'status' => $category->get_status(),
			'deleted_at' => $category->get_deleted_at() ? $category->get_deleted_at()->format('Y-m-d H:i:s') : null,
		];
	}
}
