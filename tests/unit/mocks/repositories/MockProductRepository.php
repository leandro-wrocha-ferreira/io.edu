<?php

namespace tests\unit\mocks\repositories;

use app\domain\commercial\Product;
use app\domain\commercial\repositories\ProductRepositoryInterface;
use app\models\dtos\ProductDatabase;
use app\models\mappers\ProductMapper;

/**
 * Shared mock repository for Product tests.
 */
class MockProductRepository implements ProductRepositoryInterface
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
	 * Find a product by ID.
	 *
	 * @param int $id Product ID
	 * @return Product|null
	 */
	public function find_by_id(int $id): ?Product
	{
		foreach ($this->rows as $row) {
			if ($row['id'] === $id && empty($row['deleted_at'])) {
				return ProductMapper::to_entity(new ProductDatabase($row));
			}
		}

		return null;
	}

	/**
	 * Find a product by associated course ID.
	 *
	 * @param int $course_id Course ID
	 * @return Product|null
	 */
	public function find_by_course_id(int $course_id): ?Product
	{
		foreach ($this->rows as $row) {
			if ($row['course_id'] === $course_id && empty($row['deleted_at'])) {
				return ProductMapper::to_entity(new ProductDatabase($row));
			}
		}

		return null;
	}

	/**
	 * Find a product by ID with simulated lock for update.
	 *
	 * @param int $id Product ID
	 * @return Product|null
	 */
	public function find_by_id_for_update(int $id): ?Product
	{
		return $this->find_by_id($id);
	}

	/**
	 * Save a product.
	 *
	 * @param Product $product Product entity
	 * @return Product
	 */
	public function save(Product $product): Product
	{
		if ($product->get_id() === null) {
			$data = ProductMapper::to_database_create($product);
			$data['id'] = $this->auto_increment++;
			$data['created_at'] = date('Y-m-d H:i:s');
			$data['updated_at'] = date('Y-m-d H:i:s');
			$data['deleted_at'] = null;
			$this->rows[$data['id']] = $data;

			return ProductMapper::to_entity(new ProductDatabase($data));
		}

		$id = $product->get_id();
		$data = ProductMapper::to_database_update($product);
		$data['id'] = $id;
		$data['course_id'] = $product->get_course_id();
		$data['created_at'] = $this->rows[$id]['created_at'] ?? date('Y-m-d H:i:s');
		$data['updated_at'] = date('Y-m-d H:i:s');
		$this->rows[$id] = $data;

		return ProductMapper::to_entity(new ProductDatabase($data));
	}
}
