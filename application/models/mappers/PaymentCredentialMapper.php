<?php

namespace app\models\mappers;

use app\domain\finance\PaymentCredential;
use app\domain\finance\value_objects\EncryptedSecret;
use app\models\dtos\PaymentCredentialDatabase;

/**
 * Mapper for converting between raw database data and PaymentCredential domain entity.
 */
class PaymentCredentialMapper
{
	/**
	 * Convert a raw database row DTO into a PaymentCredential domain entity.
	 *
	 * @param PaymentCredentialDatabase $row Raw database row DTO
	 * @return PaymentCredential Payment credential domain entity
	 */
	public static function to_entity(PaymentCredentialDatabase $row): PaymentCredential
	{
		return PaymentCredential::create(
			$row->provider_id,
			$row->name,
			$row->environment,
			EncryptedSecret::from_encrypted($row->api_key_encrypted),
			$row->webhook_secret_encrypted !== null && trim($row->webhook_secret_encrypted) !== ''
				? EncryptedSecret::from_encrypted($row->webhook_secret_encrypted)
				: null,
			(bool) $row->is_active,
			$row->id,
			$row->created_at,
			$row->updated_at
		);
	}

	/**
	 * Convert multiple raw database rows into an array of PaymentCredential domain entities.
	 *
	 * @param array<array<string, mixed>> $rows
	 * @return array<PaymentCredential>
	 */
	public static function to_entities(array $rows): array
	{
		if (empty($rows)) {
			return [];
		}

		$entities = [];
		foreach ($rows as $row) {
			$entities[] = self::to_entity(new PaymentCredentialDatabase($row));
		}

		return $entities;
	}

	/**
	 * Map a PaymentCredential entity to a database array for record creation.
	 *
	 * @param PaymentCredential $credential Payment credential entity
	 * @return array<string, mixed> Raw database columns map
	 */
	public static function to_database_create(PaymentCredential $credential): array
	{
		return [
			'provider_id' => $credential->get_provider_id(),
			'name' => $credential->get_name(),
			'environment' => $credential->get_environment(),
			'api_key_encrypted' => $credential->get_api_key_encrypted(),
			'webhook_secret_encrypted' => $credential->get_webhook_secret_encrypted(),
			'is_active' => $credential->is_active() ? 1 : 0,
		];
	}

	/**
	 * Map a PaymentCredential entity to a database array for record update.
	 *
	 * @param PaymentCredential $credential Payment credential entity
	 * @return array<string, mixed> Raw database columns map
	 */
	public static function to_database_update(PaymentCredential $credential): array
	{
		return [
			'provider_id' => $credential->get_provider_id(),
			'name' => $credential->get_name(),
			'environment' => $credential->get_environment(),
			'api_key_encrypted' => $credential->get_api_key_encrypted(),
			'webhook_secret_encrypted' => $credential->get_webhook_secret_encrypted(),
			'is_active' => $credential->is_active() ? 1 : 0,
		];
	}
}
