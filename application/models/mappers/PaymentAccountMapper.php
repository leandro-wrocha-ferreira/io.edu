<?php

namespace app\models\mappers;

use app\domain\finance\PaymentAccount;
use app\models\dtos\PaymentAccountDatabase;

/**
 * Mapper for converting between raw database data and PaymentAccount domain entity.
 */
class PaymentAccountMapper
{
	/**
	 * Convert a raw database row DTO into a PaymentAccount domain entity.
	 *
	 * @param PaymentAccountDatabase $row Raw database row DTO
	 * @return PaymentAccount Payment account domain entity
	 */
	public static function to_entity(PaymentAccountDatabase $row): PaymentAccount
	{
		return PaymentAccount::create(
			$row->provider_id,
			$row->credential_id,
			$row->name,
			$row->environment,
			$row->account_identifier,
			$row->document_number,
			$row->status,
			$row->id,
			$row->created_at,
			$row->updated_at
		);
	}

	/**
	 * Convert multiple raw database rows into an array of PaymentAccount domain entities.
	 *
	 * @param array<array<string, mixed>> $rows
	 * @return array<PaymentAccount>
	 */
	public static function to_entities(array $rows): array
	{
		if (empty($rows)) {
			return [];
		}

		$entities = [];
		foreach ($rows as $row) {
			$entities[] = self::to_entity(new PaymentAccountDatabase($row));
		}

		return $entities;
	}

	/**
	 * Map a PaymentAccount entity to a database array for record creation.
	 *
	 * @param PaymentAccount $account Payment account entity
	 * @return array<string, mixed> Raw database columns map
	 */
	public static function to_database_create(PaymentAccount $account): array
	{
		return [
			'provider_id' => $account->get_provider_id(),
			'credential_id' => $account->get_credential_id(),
			'name' => $account->get_name(),
			'environment' => $account->get_environment(),
			'account_identifier' => $account->get_account_identifier(),
			'document_number' => $account->get_document_number(),
			'status' => $account->get_status(),
		];
	}

	/**
	 * Map a PaymentAccount entity to a database array for record update.
	 *
	 * @param PaymentAccount $account Payment account entity
	 * @return array<string, mixed> Raw database columns map
	 */
	public static function to_database_update(PaymentAccount $account): array
	{
		return [
			'provider_id' => $account->get_provider_id(),
			'credential_id' => $account->get_credential_id(),
			'name' => $account->get_name(),
			'environment' => $account->get_environment(),
			'account_identifier' => $account->get_account_identifier(),
			'document_number' => $account->get_document_number(),
			'status' => $account->get_status(),
		];
	}
}
