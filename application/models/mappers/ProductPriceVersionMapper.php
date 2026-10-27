<?php

namespace app\models\mappers;

use app\domain\commercial\ProductPriceVersion;
use app\domain\commercial\value_objects\Price;
use app\models\dtos\ProductPriceVersionDatabase;

/**
 * Mapper for converting between raw database data and ProductPriceVersion domain entity.
 */
class ProductPriceVersionMapper
{
	/**
	 * Convert a raw database row DTO into a ProductPriceVersion domain entity.
	 *
	 * @param ProductPriceVersionDatabase $row Raw database row DTO
	 * @return ProductPriceVersion Product price version domain entity
	 */
	public static function to_entity(ProductPriceVersionDatabase $row): ProductPriceVersion
	{
		$previous_price = $row->previous_price_in_cents !== null ? Price::from_cents($row->previous_price_in_cents, $row->currency) : null;
		$new_price = $row->new_price_in_cents !== null ? Price::from_cents($row->new_price_in_cents, $row->currency) : null;
		$is_free = (bool) $row->is_free;

		return ProductPriceVersion::create(
			$row->product_id,
			$row->version_number,
			$previous_price,
			$new_price,
			$row->snapshot,
			$row->change_reason,
			$row->created_by,
			$is_free,
			$row->id,
			$row->created_at,
			$row->currency
		);
	}

	/**
	 * Convert multiple raw database rows into an array of ProductPriceVersion domain entities.
	 *
	 * @param array<array<string, mixed>> $rows
	 * @return array<ProductPriceVersion>
	 */
	public static function to_entities(array $rows): array
	{
		if (empty($rows)) {
			return [];
		}

		$entities = [];
		foreach ($rows as $row) {
			$entities[] = self::to_entity(new ProductPriceVersionDatabase($row));
		}

		return $entities;
	}

	/**
	 * Map a ProductPriceVersion entity to a database array for record creation.
	 *
	 * @param ProductPriceVersion $version Product price version entity
	 * @return array<string, mixed> Raw database columns map
	 */
	public static function to_database_create(ProductPriceVersion $version): array
	{
		return [
			'product_id' => $version->get_product_id(),
			'version_number' => $version->get_version_number(),
			'is_free' => $version->is_free() ? 1 : 0,
			'previous_price_in_cents' => $version->get_previous_price() !== null ? $version->get_previous_price()->get_cents() : null,
			'new_price_in_cents' => $version->is_free() || $version->get_new_price() === null ? null : $version->get_new_price()->get_cents(),
			'currency' => $version->get_currency(),
			'snapshot' => json_encode($version->get_snapshot()),
			'change_reason' => $version->get_change_reason(),
			'created_by' => $version->get_created_by(),
		];
	}
}
