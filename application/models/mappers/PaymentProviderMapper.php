<?php

namespace app\models\mappers;

use app\domain\finance\PaymentProvider;
use app\models\dtos\PaymentProviderDatabase;

/**
 * Mapper for converting between raw database data and PaymentProvider domain entity.
 */
class PaymentProviderMapper
{
	/**
	 * Convert a raw database row DTO into a PaymentProvider domain entity.
	 *
	 * @param PaymentProviderDatabase $row Raw database row DTO
	 * @return PaymentProvider Payment provider domain entity
	 */
	public static function to_entity(PaymentProviderDatabase $row): PaymentProvider
	{
		return PaymentProvider::create(
			$row->name,
			$row->slug,
			$row->supported_methods,
			(bool) $row->is_active,
			$row->id,
			$row->created_at,
			$row->updated_at
		);
	}

	/**
	 * Convert multiple raw database rows into an array of PaymentProvider domain entities.
	 *
	 * @param array<array<string, mixed>> $rows
	 * @return array<PaymentProvider>
	 */
	public static function to_entities(array $rows): array
	{
		if (empty($rows)) {
			return [];
		}

		$entities = [];
		foreach ($rows as $row) {
			$entities[] = self::to_entity(new PaymentProviderDatabase($row));
		}

		return $entities;
	}

	/**
	 * Map a PaymentProvider entity to a database array for record creation.
	 *
	 * @param PaymentProvider $provider Payment provider entity
	 * @return array<string, mixed> Raw database columns map
	 */
	public static function to_database_create(PaymentProvider $provider): array
	{
		return [
			'name' => $provider->get_name(),
			'slug' => $provider->get_slug(),
			'supported_methods' => json_encode($provider->get_supported_methods()),
			'is_active' => $provider->is_active() ? 1 : 0,
		];
	}

	/**
	 * Map a PaymentProvider entity to a database array for record update.
	 *
	 * @param PaymentProvider $provider Payment provider entity
	 * @return array<string, mixed> Raw database columns map
	 */
	public static function to_database_update(PaymentProvider $provider): array
	{
		return [
			'name' => $provider->get_name(),
			'slug' => $provider->get_slug(),
			'supported_methods' => json_encode($provider->get_supported_methods()),
			'is_active' => $provider->is_active() ? 1 : 0,
		];
	}
}
