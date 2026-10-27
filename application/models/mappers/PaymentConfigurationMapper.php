<?php

namespace app\models\mappers;

use app\domain\finance\PaymentConfiguration;
use app\models\dtos\PaymentConfigurationDatabase;

/**
 * Mapper for converting between raw database data and PaymentConfiguration domain entity.
 */
class PaymentConfigurationMapper
{
	/**
	 * Convert a raw database row DTO into a PaymentConfiguration domain entity.
	 *
	 * @param PaymentConfigurationDatabase $row Raw database row DTO
	 * @return PaymentConfiguration Payment configuration domain entity
	 */
	public static function to_entity(PaymentConfigurationDatabase $row): PaymentConfiguration
	{
		return PaymentConfiguration::create(
			$row->name,
			$row->payment_account_id,
			$row->allowed_methods,
			$row->description,
			(bool) $row->is_active,
			$row->id,
			$row->created_at,
			$row->updated_at,
			$row->deleted_at,
			$row->account_name,
			$row->account_identifier,
			$row->environment,
			$row->provider_name,
			$row->provider_slug
		);
	}

	/**
	 * Convert multiple raw database rows into an array of PaymentConfiguration domain entities.
	 *
	 * @param array<array<string, mixed>> $rows
	 * @return array<PaymentConfiguration>
	 */
	public static function to_entities(array $rows): array
	{
		if (empty($rows)) {
			return [];
		}

		$entities = [];
		foreach ($rows as $row) {
			$entities[] = self::to_entity(new PaymentConfigurationDatabase($row));
		}

		return $entities;
	}

	/**
	 * Map a PaymentConfiguration entity to a database array for record creation.
	 *
	 * @param PaymentConfiguration $config Payment configuration entity
	 * @return array<string, mixed> Raw database columns map
	 */
	public static function to_database_create(PaymentConfiguration $config): array
	{
		return [
			'name' => $config->get_name(),
			'payment_account_id' => $config->get_payment_account_id(),
			'allowed_methods' => json_encode($config->get_allowed_methods()),
			'description' => $config->get_description(),
			'is_active' => $config->is_active() ? 1 : 0,
		];
	}

	/**
	 * Map a PaymentConfiguration entity to a database array for record update.
	 *
	 * @param PaymentConfiguration $config Payment configuration entity
	 * @return array<string, mixed> Raw database columns map
	 */
	public static function to_database_update(PaymentConfiguration $config): array
	{
		return [
			'name' => $config->get_name(),
			'payment_account_id' => $config->get_payment_account_id(),
			'allowed_methods' => json_encode($config->get_allowed_methods()),
			'description' => $config->get_description(),
			'is_active' => $config->is_active() ? 1 : 0,
			'deleted_at' => $config->get_deleted_at() ? $config->get_deleted_at()->format('Y-m-d H:i:s') : null,
		];
	}
}
