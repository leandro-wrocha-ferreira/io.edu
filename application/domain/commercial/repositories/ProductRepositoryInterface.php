<?php

namespace app\domain\commercial\repositories;

use app\domain\commercial\Product;

/**
 * Repository interface for Product persistence.
 */
interface ProductRepositoryInterface
{
	/**
	 * Find a product by its ID.
	 *
	 * @param int $id Product ID
	 * @return Product|null
	 */
	public function find_by_id(int $id): ?Product;

	/**
	 * Find a product by its associated course ID.
	 *
	 * @param int $course_id Course ID
	 * @return Product|null
	 */
	public function find_by_course_id(int $course_id): ?Product;

	/**
	 * Find a product by its ID locking the row for update (pessimistic lock).
	 *
	 * @param int $id Product ID
	 * @return Product|null
	 */
	public function find_by_id_for_update(int $id): ?Product;

	/**
	 * Save (insert or update) a product.
	 *
	 * @param Product $product Product entity
	 * @return Product Persisted product with ID
	 */
	public function save(Product $product): Product;
}
