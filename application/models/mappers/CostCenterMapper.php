<?php

namespace app\models\mappers;

use app\domain\finance\CostCenter;
use app\models\dtos\CostCenterDatabase;

/**
 * Mapper for converting between raw database data and CostCenter domain entity.
 */
class CostCenterMapper
{
	/**
	 * Convert a raw database row DTO into a CostCenter domain entity.
	 *
	 * @param CostCenterDatabase $row Raw database row DTO
	 * @return CostCenter Cost center domain entity
	 */
	public static function to_entity(CostCenterDatabase $row): CostCenter
	{
		return CostCenter::create(
			$row->name,
			$row->description,
			(bool) $row->is_active,
			$row->id,
			$row->created_at,
			$row->updated_at,
			$row->deleted_at
		);
	}

	/**
	 * Convert multiple raw database rows into an array of CostCenter domain entities.
	 *
	 * @param array<array<string, mixed>> $rows
	 * @return array<CostCenter>
	 */
	public static function to_entities(array $rows): array
	{
		if (empty($rows)) {
			return [];
		}

		$entities = [];
		foreach ($rows as $row) {
			$entities[] = self::to_entity(new CostCenterDatabase($row));
		}

		return $entities;
	}

	/**
	 * Map a CostCenter entity to a database array for record creation.
	 *
	 * @param CostCenter $cost_center Cost center entity
	 * @return array<string, mixed> Raw database columns map
	 */
	public static function to_database_create(CostCenter $cost_center): array
	{
		return [
			'name' => $cost_center->get_name(),
			'description' => $cost_center->get_description(),
			'is_active' => $cost_center->is_active() ? 1 : 0,
		];
	}

	/**
	 * Map a CostCenter entity to a database array for record update.
	 *
	 * @param CostCenter $cost_center Cost center entity
	 * @return array<string, mixed> Raw database columns map
	 */
	public static function to_database_update(CostCenter $cost_center): array
	{
		return [
			'name' => $cost_center->get_name(),
			'description' => $cost_center->get_description(),
			'is_active' => $cost_center->is_active() ? 1 : 0,
			'deleted_at' => $cost_center->get_deleted_at() ? $cost_center->get_deleted_at()->format('Y-m-d H:i:s') : null,
		];
	}
}
