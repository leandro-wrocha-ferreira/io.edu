<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use app\domain\course\Category;
use app\domain\course\repositories\CategoryRepositoryInterface;
use app\models\dtos\CategoryDatabase;
use app\models\mappers\CategoryMapper;

/**
 * Category model implementing CategoryRepositoryInterface.
 *
 * Handles persistence for the Category entity using MY_Model pure data CRUD engine
 * and delegates entity-database mapping to CategoryMapper and CategoryDatabase DTO.
 */
class Category_model extends MY_Model implements CategoryRepositoryInterface
{
	/**
	 * Table name.
	 *
	 * @var string
	 */
	protected string $table = 'categories';

	/**
	 * Constructor.
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Find a category by its ID.
	 *
	 * @param int $id Category ID
	 * @return Category|null
	 */
	public function find_by_id(int $id): ?Category
	{
		$row = $this->db
			->where('categories.id', $id)
			->where('categories.deleted_at IS NULL', null, false)
			->get($this->table)
			->row_array();

		if ($row === null) {
			return null;
		}

		return CategoryMapper::to_entity(new CategoryDatabase($row));
	}

	/**
	 * Find a category by its slug.
	 *
	 * @param string $slug Unique slug
	 * @return Category|null
	 */
	public function find_by_slug(string $slug): ?Category
	{
		$row = $this->db
			->where('categories.slug', $slug)
			->where('categories.deleted_at IS NULL', null, false)
			->get($this->table)
			->row_array();

		if ($row === null) {
			return null;
		}

		return CategoryMapper::to_entity(new CategoryDatabase($row));
	}

	/**
	 * Find all non-deleted categories.
	 *
	 * @return array<Category>
	 */
	public function find_all(): array
	{
		$rows = $this->db
			->where('categories.deleted_at IS NULL', null, false)
			->order_by('categories.name', 'ASC')
			->get($this->table)
			->result_array();

		return CategoryMapper::to_entities($rows);
	}

	/**
	 * Find all active and non-deleted categories.
	 *
	 * @return array<Category>
	 */
	public function find_active(): array
	{
		$rows = $this->db
			->where('categories.status', 'active')
			->where('categories.deleted_at IS NULL', null, false)
			->order_by('categories.name', 'ASC')
			->get($this->table)
			->result_array();

		return CategoryMapper::to_entities($rows);
	}

	/**
	 * Find categories with server-side pagination and search.
	 *
	 * @param int $start Offset
	 * @param int $length Page size
	 * @param string $search Global search term
	 * @param string $order_col Column name
	 * @param string $order_dir ASC or DESC
	 * @return array{data: array<Category>, recordsFiltered: int}
	 */
	public function find_paginated(int $start, int $length, string $search, string $order_col, string $order_dir): array
	{
		$total_filtered = $this->_count_paginated($search);

		$allowed = ['id', 'name', 'slug', 'status', 'created_at'];
		$col = in_array($order_col, $allowed, true) ? 'categories.' . $order_col : 'categories.name';
		$dir = strtoupper($order_dir) === 'ASC' ? 'ASC' : 'DESC';

		$this->db
			->where('categories.deleted_at IS NULL', null, false)
			->order_by($col, $dir)
			->limit($length, $start);

		if ($search !== '') {
			$this->db->group_start()
				->like('categories.name', $search)
				->or_like('categories.slug', $search)
				->group_end();
		}

		$rows = $this->db->get($this->table)->result_array();

		return [
			'data' => CategoryMapper::to_entities($rows),
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
		return $this->db
			->where('categories.deleted_at IS NULL', null, false)
			->from($this->table)
			->count_all_results();
	}

	/**
	 * Create a new category record.
	 *
	 * @param Category $category
	 * @return Category
	 */
	public function create(Category $category): Category
	{
		$data = CategoryMapper::to_database_create($category);
		$insert_id = (int) $this->insert($data);

		$row = $this->db
			->where($this->primary_key, $insert_id)
			->get($this->table)
			->row_array();

		return CategoryMapper::to_entity(new CategoryDatabase($row));
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

		$data = CategoryMapper::to_database_update($category);
		$this->update($data, [$this->primary_key => $category->get_id()]);

		$row = $this->db
			->where($this->primary_key, $category->get_id())
			->get($this->table)
			->row_array();

		return $row !== null ? CategoryMapper::to_entity(new CategoryDatabase($row)) : $category;
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

	/**
	 * Count filtered categories for DataTables pagination.
	 *
	 * @param string $search Global search term
	 * @return int
	 */
	private function _count_paginated(string $search): int
	{
		$this->db->where('categories.deleted_at IS NULL', null, false);

		if ($search !== '') {
			$this->db->group_start()
				->like('categories.name', $search)
				->or_like('categories.slug', $search)
				->group_end();
		}

		return $this->db->from($this->table)->count_all_results();
	}
}
