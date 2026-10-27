<?php

namespace tests\unit\mocks\repositories;

use app\domain\commercial\ProductPriceVersion;
use app\domain\commercial\repositories\ProductPriceVersionRepositoryInterface;
use app\models\dtos\ProductPriceVersionDatabase;
use app\models\mappers\ProductPriceVersionMapper;

/**
 * Shared mock repository for ProductPriceVersion tests.
 */
class MockProductPriceVersionRepository implements ProductPriceVersionRepositoryInterface
{
	/**
	 * Simulated raw database rows.
	 *
	 * @var array<int, array<string, mixed>>
	 */
	private array $rows = [];

	/**
	 * Auto-increment counter.
	 *
	 * @var int
	 */
	private int $auto_increment = 1;

	/**
	 * Get next version number for a product.
	 *
	 * @param int $product_id Product ID
	 * @return int Next version number
	 */
	public function get_next_version_number(int $product_id): int
	{
		$max_version = 0;
		foreach ($this->rows as $row) {
			if ($row['product_id'] === $product_id && $row['version_number'] > $max_version) {
				$max_version = $row['version_number'];
			}
		}

		return $max_version + 1;
	}

	/**
	 * Find the latest price version for a product.
	 *
	 * @param int $product_id Product ID
	 * @return ProductPriceVersion|null
	 */
	public function find_latest_by_product_id(int $product_id): ?ProductPriceVersion
	{
		$versions = $this->list_by_product_id($product_id);
		return !empty($versions) ? $versions[0] : null;
	}

	/**
	 * List all price versions for a product ordered chronologically descending.
	 *
	 * @param int $product_id Product ID
	 * @return array<ProductPriceVersion>
	 */
	public function list_by_product_id(int $product_id): array
	{
		$matching_rows = [];
		foreach ($this->rows as $row) {
			if ($row['product_id'] === $product_id) {
				$matching_rows[] = $row;
			}
		}

		usort($matching_rows, function (array $item_a, array $item_b) {
			return $item_b['version_number'] <=> $item_a['version_number'];
		});

		return ProductPriceVersionMapper::to_entities($matching_rows);
	}

	/**
	 * Save an immutable price version.
	 *
	 * @param ProductPriceVersion $version Version to persist
	 * @return ProductPriceVersion
	 */
	public function save(ProductPriceVersion $version): ProductPriceVersion
	{
		$data = ProductPriceVersionMapper::to_database_create($version);
		$data['id'] = $this->auto_increment++;
		$data['created_at'] = date('Y-m-d H:i:s');
		$this->rows[$data['id']] = $data;

		return ProductPriceVersionMapper::to_entity(new ProductPriceVersionDatabase($data));
	}
}
