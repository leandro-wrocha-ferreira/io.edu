<?php

namespace app\models\mappers;

use app\domain\identity\Role;
use app\models\dtos\RoleDatabase;

/**
 * Mapper for converting between raw database data and Role domain entity.
 */
class RoleMapper
{
	/**
	 * Convert a raw database row into a Role domain entity.
	 *
	 * @param RoleDatabase|null $row
	 * @return Role|null
	 */
	public static function to_entity(?RoleDatabase $row): ?Role
	{
		if (empty($row)) {
			return null;
		}

		return Role::create(
			$row->name,
			$row->slug,
			$row->description,
			$row->id,
			$row->created_at,
			$row->updated_at
		);
	}

	/**
	 * Convert multiple raw database rows into an array of Role domain entities.
	 *
	 * @param array<array<string, mixed>> $rows
	 * @return array<Role>
	 */
	public static function to_entities(array $rows): array
	{
		if (empty($rows)) {
			return [];
		}

		$entities = [];
		foreach ($rows as $row) {
			$entity = self::to_entity(new RoleDatabase($row));
			if ($entity !== null) {
				$entities[] = $entity;
			}
		}

		return $entities;
	}

	/**
	 * Map a Role entity to a database array for record creation.
	 *
	 * @param Role $role
	 * @return array<string, mixed>
	 */
	public static function to_database_create(Role $role): array
	{
		return [
			'name' => $role->get_name(),
			'slug' => $role->get_slug(),
			'description' => $role->get_description(),
		];
	}

	/**
	 * Map a Role entity to a database array for record update.
	 *
	 * @param Role $role
	 * @return array<string, mixed>
	 */
	public static function to_database_update(Role $role): array
	{
		return [
			'name' => $role->get_name(),
			'slug' => $role->get_slug(),
			'description' => $role->get_description(),
		];
	}
}
