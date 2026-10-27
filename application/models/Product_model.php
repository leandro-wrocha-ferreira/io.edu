<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use app\domain\commercial\Product;
use app\domain\commercial\repositories\ProductRepositoryInterface;
use app\models\dtos\ProductDatabase;
use app\models\mappers\ProductMapper;

/**
 * Product model implementing ProductRepositoryInterface.
 */
class Product_model extends MY_Model implements ProductRepositoryInterface
{
	/**
	 * Table name.
	 *
	 * @var string
	 */
	protected string $table = 'products';

	/**
	 * Constructor.
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Find a product by its ID.
	 *
	 * @param int $id Product ID
	 * @return Product|null
	 */
	public function find_by_id(int $id): ?Product
	{
		$row = $this->db->from($this->table)
			->where('id', $id)
			->where('deleted_at IS NULL', null, false)
			->get()
			->row_array();

		if ($row === null) {
			return null;
		}

		return ProductMapper::to_entity(new ProductDatabase($row));
	}

	/**
	 * Find a product by its associated course ID.
	 *
	 * @param int $course_id Course ID
	 * @return Product|null
	 */
	public function find_by_course_id(int $course_id): ?Product
	{
		$row = $this->db->from($this->table)
			->where('course_id', $course_id)
			->where('deleted_at IS NULL', null, false)
			->get()
			->row_array();

		if ($row === null) {
			return null;
		}

		return ProductMapper::to_entity(new ProductDatabase($row));
	}

	/**
	 * Find a product by its ID locking the row for update (pessimistic lock).
	 *
	 * @param int $id Product ID
	 * @return Product|null
	 */
	public function find_by_id_for_update(int $id): ?Product
	{
		$sql = "SELECT * FROM `{$this->table}` WHERE `id` = ? AND `deleted_at` IS NULL FOR UPDATE";
		$query = $this->db->query($sql, [$id]);
		$row = $query->row_array();

		if ($row === null) {
			return null;
		}

		return ProductMapper::to_entity(new ProductDatabase($row));
	}

	/**
	 * Create a new product record.
	 *
	 * @param Product $product
	 * @return Product
	 */
	public function create(Product $product): Product
	{
		$data = ProductMapper::to_database_create($product);
		$insert_id = (int) $this->insert($data);

		return $this->find_by_id($insert_id) ?? $product;
	}

	/**
	 * Save (insert or update) a product.
	 *
	 * @param Product $product Product entity
	 * @return Product Persisted product with ID
	 */
	public function save(Product $product): Product
	{
		if ($product->get_id() === null) {
			return $this->create($product);
		}

		$data = ProductMapper::to_database_update($product);
		$this->update($data, [$this->primary_key => $product->get_id()]);

		return $this->find_by_id($product->get_id()) ?? $product;
	}
}
