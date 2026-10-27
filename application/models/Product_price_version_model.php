<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use app\domain\commercial\ProductPriceVersion;
use app\domain\commercial\repositories\ProductPriceVersionRepositoryInterface;
use app\models\dtos\ProductPriceVersionDatabase;
use app\models\mappers\ProductPriceVersionMapper;

/**
 * Product price version model implementing ProductPriceVersionRepositoryInterface.
 */
class Product_price_version_model extends MY_Model implements ProductPriceVersionRepositoryInterface
{
	/**
	 * Table name.
	 *
	 * @var string
	 */
	protected string $table = 'product_price_versions';

	/**
	 * Constructor.
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Get the next sequential version number for a product.
	 *
	 * @param int $product_id Product ID
	 * @return int Next version number
	 */
	public function get_next_version_number(int $product_id): int
	{
		$row = $this->db->select('COALESCE(MAX(version_number), 0) + 1 AS next_version', false)
			->from($this->table)
			->where('product_id', $product_id)
			->get()
			->row_array();

		return isset($row['next_version']) ? (int) $row['next_version'] : 1;
	}

	/**
	 * Find the latest price version for a product.
	 *
	 * @param int $product_id Product ID
	 * @return ProductPriceVersion|null
	 */
	public function find_latest_by_product_id(int $product_id): ?ProductPriceVersion
	{
		$row = $this->db->from($this->table)
			->where('product_id', $product_id)
			->order_by('version_number', 'DESC')
			->limit(1)
			->get()
			->row_array();

		if ($row === null) {
			return null;
		}

		return ProductPriceVersionMapper::to_entity(new ProductPriceVersionDatabase($row));
	}

	/**
	 * List all price versions for a product ordered chronologically descending.
	 *
	 * @param int $product_id Product ID
	 * @return array<ProductPriceVersion>
	 */
	public function list_by_product_id(int $product_id): array
	{
		$rows = $this->db->from($this->table)
			->where('product_id', $product_id)
			->order_by('version_number', 'DESC')
			->get()
			->result_array();

		return ProductPriceVersionMapper::to_entities($rows);
	}

	/**
	 * Save (insert only) an immutable price version.
	 *
	 * @param ProductPriceVersion $version Version to persist
	 * @return ProductPriceVersion Persisted version
	 */
	public function save(ProductPriceVersion $version): ProductPriceVersion
	{
		$data = ProductPriceVersionMapper::to_database_create($version);
		$insert_id = (int) $this->insert($data);

		$row = $this->db->from($this->table)
			->where('id', $insert_id)
			->get()
			->row_array();

		if ($row === null) {
			return $version;
		}

		return ProductPriceVersionMapper::to_entity(new ProductPriceVersionDatabase($row));
	}
}
