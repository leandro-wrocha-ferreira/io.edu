<?php

namespace app\models\mappers;

use app\domain\authorization\Permission;
use app\models\dtos\PermissionDatabase;

/**
 * Mapper for converting between raw database data and Permission domain entity.
 */
class PermissionMapper
{
	/**
	 * Convert a raw database row into a Permission domain entity.
	 *
	 * @param PermissionDatabase|null $row
	 * @return Permission|null
	 */
	public static function to_entity(?PermissionDatabase $row): ?Permission
	{
		if (empty($row)) {
			return null;
		}

		return Permission::create(
			$row->name,
			$row->slug,
			$row->description,
			$row->id,
			$row->created_at,
			$row->updated_at
		);
	}

	/**
	 * Convert multiple raw database rows into an array of Permission domain entities.
	 *
	 * @param array<array<string, mixed>> $rows
	 * @return array<Permission>
	 */
	public static function to_entities(array $rows): array
	{
		if (empty($rows)) {
			return [];
		}

		$entities = [];
		foreach ($rows as $row) {
			$entity = self::to_entity(new PermissionDatabase($row));
			if ($entity !== null) {
				$entities[] = $entity;
			}
		}

		return $entities;
	}

	/**
	 * Map a Permission entity to a database array for record creation.
	 *
	 * @param Permission $permission
	 * @return array<string, mixed>
	 */
	public static function to_database_create(Permission $permission): array
	{
		return [
			'name' => $permission->get_name(),
			'slug' => $permission->get_slug(),
			'description' => $permission->get_description(),
		];
	}

	/**
	 * Map a Permission entity to a database array for record update.
	 *
	 * @param Permission $permission
	 * @return array<string, mixed>
	 */
	public static function to_database_update(Permission $permission): array
	{
		return [
			'name' => $permission->get_name(),
			'slug' => $permission->get_slug(),
			'description' => $permission->get_description(),
		];
	}
}
