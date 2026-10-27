<?php

namespace app\models\mappers;

use app\domain\commercial\Product;
use app\domain\commercial\value_objects\BillingModel;
use app\domain\commercial\value_objects\EnabledPaymentMethods;
use app\domain\commercial\value_objects\InstallmentLimit;
use app\domain\commercial\value_objects\Price;
use app\domain\commercial\value_objects\SubscriptionInterval;
use app\models\dtos\ProductDatabase;

/**
 * Mapper for converting between raw database data and Product domain entity.
 */
class ProductMapper
{
	/**
	 * Convert a raw database row DTO into a Product domain entity.
	 *
	 * @param ProductDatabase $row Raw database row DTO
	 * @return Product Product domain entity
	 */
	public static function to_entity(ProductDatabase $row): Product
	{
		$is_free = (bool) $row->is_free;
		$price = (!$is_free && $row->price_in_cents !== null)
			? Price::from_cents($row->price_in_cents, $row->currency)
			: null;
		$billing_model = BillingModel::from_string($row->billing_model);
		$max_installments = InstallmentLimit::from_int($row->max_installments);
		$subscription_interval = $row->subscription_interval !== null ? SubscriptionInterval::from_string($row->subscription_interval) : null;
		$payment_methods = new EnabledPaymentMethods($row->payment_methods);

		return Product::create(
			$row->course_id,
			$is_free,
			$price,
			$billing_model,
			$max_installments,
			$subscription_interval,
			$row->max_billing_cycles,
			$payment_methods,
			$row->payment_configuration_id,
			$row->cost_center_id,
			$row->id,
			$row->created_at,
			$row->updated_at,
			$row->deleted_at
		);
	}

	/**
	 * Convert multiple raw database rows into an array of Product domain entities.
	 *
	 * @param array<array<string, mixed>> $rows
	 * @return array<Product>
	 */
	public static function to_entities(array $rows): array
	{
		if (empty($rows)) {
			return [];
		}

		$entities = [];
		foreach ($rows as $row) {
			$entities[] = self::to_entity(new ProductDatabase($row));
		}

		return $entities;
	}

	/**
	 * Map a Product entity to a database array for record creation.
	 *
	 * @param Product $product Product entity
	 * @return array<string, mixed> Raw database columns map
	 */
	public static function to_database_create(Product $product): array
	{
		return [
			'course_id' => $product->get_course_id(),
			'is_free' => $product->is_free() ? 1 : 0,
			'price_in_cents' => $product->is_free() || $product->get_price() === null ? null : $product->get_price()->get_cents(),
			'currency' => $product->get_price() ? $product->get_price()->get_currency() : 'BRL',
			'billing_model' => (string) $product->get_billing_model(),
			'max_installments' => $product->get_max_installments()->get_limit(),
			'subscription_interval' => $product->get_subscription_interval() !== null ? (string) $product->get_subscription_interval() : null,
			'max_billing_cycles' => $product->get_max_billing_cycles(),
			'payment_methods' => json_encode($product->get_payment_methods()->to_array()),
			'payment_configuration_id' => $product->get_payment_configuration_id(),
			'cost_center_id' => $product->get_cost_center_id(),
		];
	}

	/**
	 * Map a Product entity to a database array for record update.
	 *
	 * @param Product $product Product entity
	 * @return array<string, mixed> Raw database columns map
	 */
	public static function to_database_update(Product $product): array
	{
		return [
			'is_free' => $product->is_free() ? 1 : 0,
			'price_in_cents' => $product->is_free() || $product->get_price() === null ? null : $product->get_price()->get_cents(),
			'currency' => $product->get_price() ? $product->get_price()->get_currency() : 'BRL',
			'billing_model' => (string) $product->get_billing_model(),
			'max_installments' => $product->get_max_installments()->get_limit(),
			'subscription_interval' => $product->get_subscription_interval() !== null ? (string) $product->get_subscription_interval() : null,
			'max_billing_cycles' => $product->get_max_billing_cycles(),
			'payment_methods' => json_encode($product->get_payment_methods()->to_array()),
			'payment_configuration_id' => $product->get_payment_configuration_id(),
			'cost_center_id' => $product->get_cost_center_id(),
			'deleted_at' => $product->get_deleted_at() ? $product->get_deleted_at()->format('Y-m-d H:i:s') : null,
		];
	}
}
