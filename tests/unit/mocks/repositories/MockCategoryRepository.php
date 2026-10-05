<?php

namespace tests\unit\mocks\repositories;

use app\domain\course\Category;
use app\domain\course\repositories\CategoryRepositoryInterface;
use app\models\dtos\CategoryDatabase;
use app\models\mappers\CategoryMapper;

/**
 * Shared mock repository for Category tests.
 *
 * Simulates raw database storage and validates DTO/Mapper transformations.
 */
class MockCategoryRepository implements CategoryRepositoryInterface
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
	 * Find a category by ID.
	 *
	 * @param int $id Category ID
	 * @return Category|null
	 */
	public function find_by_id(int $id): ?Category
	{
		foreach ($this->rows as $row) {
			if ($row['id'] === $id && empty($row['deleted_at'])) {
				return CategoryMapper::to_entity(new CategoryDatabase($row));
			}
		}
		return null;
	}

	/**
	 * Find a category by slug.
	 *
	 * @param string $slug Unique slug
	 * @return Category|null
	 */
	public function find_by_slug(string $slug): ?Category
	{
		foreach ($this->rows as $row) {
			if ($row['slug'] === $slug && empty($row['deleted_at'])) {
				return CategoryMapper::to_entity(new CategoryDatabase($row));
			}
		}
		return null;
	}

	/**
	 * Find all non-deleted categories.
	 *
	 * @return array<Category>
	 */
	public function find_all(): array
	{
		$active_rows = array_filter($this->rows, function (array $row) {
			return empty($row['deleted_at']);
		});

		return CategoryMapper::to_entities(array_values($active_rows));
	}

	/**
	 * Find all active and non-deleted categories.
	 *
	 * @return array<Category>
	 */
	public function find_active(): array
	{
		$active_rows = array_filter($this->rows, function (array $row) {
			return empty($row['deleted_at']) && $row['status'] === 'active';
		});

		return CategoryMapper::to_entities(array_values($active_rows));
	}

	/**
	 * Find categories with server-side pagination and search.
	 *
	 * @param int $start Offset
	 * @param int $length Limit
	 * @param string $search Global search
	 * @param string $order_col Column name
	 * @param string $order_dir Direction
	 * @return array{data: array<Category>, recordsFiltered: int}
	 */
	public function find_paginated(int $start, int $length, string $search, string $order_col, string $order_dir): array
	{
		$filtered_rows = array_filter($this->rows, function (array $row) use ($search) {
			if (!empty($row['deleted_at'])) {
				return false;
			}
			if ($search !== '') {
				$name_match = stripos((string) $row['name'], $search) !== false;
				$slug_match = stripos((string) $row['slug'], $search) !== false;
				return $name_match || $slug_match;
			}
			return true;
		});

		$total_filtered = count($filtered_rows);
		$sliced = array_slice(array_values($filtered_rows), $start, $length);

		return [
			'data' => CategoryMapper::to_entities($sliced),
			'recordsFiltered' => $total_filtered,
		];
	}

	/**
	 * Count total non-deleted categories.
	 *
	 * @return int
	 */
	public function count_all(): int
	{
		$non_deleted = array_filter($this->rows, function (array $row) {
			return empty($row['deleted_at']);
		});

		return count($non_deleted);
	}

	/**
	 * Create a new category.
	 *
	 * @param Category $category
	 * @return Category
	 */
	public function create(Category $category): Category
	{
		$data = CategoryMapper::to_database_create($category);
		$data['id'] = $this->auto_increment++;
		$data['created_at'] = date('Y-m-d H:i:s');
		$data['updated_at'] = date('Y-m-d H:i:s');
		$data['deleted_at'] = null;

		$this->rows[$data['id']] = $data;

		return CategoryMapper::to_entity(new CategoryDatabase($data));
	}

	/**
	 * Save (insert or update) a category.
	 *
	 * @param Category $category
	 * @return Category
	 */
	public function save(Category $category): Category
	{
		if ($category->get_id() === null) {
			return $this->create($category);
		}

		$category_id = $category->get_id();
		$update_data = CategoryMapper::to_database_update($category);

		if (isset($this->rows[$category_id])) {
			$existing = $this->rows[$category_id];
			$merged = array_merge($existing, $update_data, [
				'updated_at' => date('Y-m-d H:i:s'),
			]);
			$this->rows[$category_id] = $merged;
			return CategoryMapper::to_entity(new CategoryDatabase($merged));
		}

		$update_data['id'] = $category_id;
		$update_data['created_at'] = date('Y-m-d H:i:s');
		$update_data['updated_at'] = date('Y-m-d H:i:s');
		$this->rows[$category_id] = $update_data;

		return CategoryMapper::to_entity(new CategoryDatabase($update_data));
	}

	/**
	 * Delete a category (soft delete).
	 *
	 * @param Category $category
	 * @return Category
	 */
	public function delete(Category $category): Category
	{
		$category->delete();
		return $this->save($category);
	}
}
