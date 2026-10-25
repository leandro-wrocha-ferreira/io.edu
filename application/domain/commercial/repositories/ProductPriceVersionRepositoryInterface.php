<?php

namespace app\domain\commercial\repositories;

use app\domain\commercial\ProductPriceVersion;

/**
 * Repository interface for immutable ProductPriceVersion persistence.
 */
interface ProductPriceVersionRepositoryInterface
{
	/**
	 * Get the next sequential version number for a product.
	 *
	 * @param int $product_id Product ID
	 * @return int Next version number (e.g. 1 if none exists, max+1 otherwise)
	 */
	public function get_next_version_number(int $product_id): int;

	/**
	 * Find the latest price version for a product.
	 *
	 * @param int $product_id Product ID
	 * @return ProductPriceVersion|null
	 */
	public function find_latest_by_product_id(int $product_id): ?ProductPriceVersion;

	/**
	 * List all price versions for a product ordered chronologically descending.
	 *
	 * @param int $product_id Product ID
	 * @return array<ProductPriceVersion>
	 */
	public function list_by_product_id(int $product_id): array;

	/**
	 * Save (insert only) an immutable price version.
	 *
	 * @param ProductPriceVersion $version Version to persist
	 * @return ProductPriceVersion Persisted version
	 */
	public function save(ProductPriceVersion $version): ProductPriceVersion;
}
