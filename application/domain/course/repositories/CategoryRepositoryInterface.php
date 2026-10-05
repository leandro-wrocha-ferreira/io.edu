<?php

namespace app\domain\course\repositories;

use app\domain\course\Category;

/**
 * Repository interface for Category persistence.
 */
interface CategoryRepositoryInterface
{
	/**
	 * Find a category by its ID.
	 *
	 * @param int $id Category ID
	 * @return Category|null
	 */
	public function find_by_id(int $id): ?Category;

	/**
	 * Find a category by its unique slug.
	 *
	 * @param string $slug
	 * @return Category|null
	 */
	public function find_by_slug(string $slug): ?Category;

	/**
	 * Find all non-deleted categories.
	 *
	 * @return array<Category>
	 */
	public function find_all(): array;

	/**
	 * Find all active and non-deleted categories.
	 *
	 * @return array<Category>
	 */
	public function find_active(): array;

	/**
	 * Find categories with server-side pagination and search.
	 *
	 * @param int $start
	 * @param int $length
	 * @param string $search
	 * @param string $order_col
	 * @param string $order_dir
	 * @return array{data: array<Category>, recordsFiltered: int}
	 */
	public function find_paginated(int $start, int $length, string $search, string $order_col, string $order_dir): array;

	/**
	 * Count total non-deleted categories.
	 *
	 * @return int
	 */
	public function count_all(): int;

	/**
	 * Create a new category record.
	 *
	 * @param Category $category
	 * @return Category
	 */
	public function create(Category $category): Category;

	/**
	 * Save (insert or update) a category.
	 *
	 * @param Category $category
	 * @return Category
	 */
	public function save(Category $category): Category;

	/**
	 * Delete a category (soft delete).
	 *
	 * @param Category $category
	 * @return Category
	 */
	public function delete(Category $category): Category;
}
