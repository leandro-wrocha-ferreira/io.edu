<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use app\domain\course\Course;
use app\domain\course\repositories\CourseRepositoryInterface;
use app\domain\course\value_objects\CourseSlug;
use app\models\dtos\CourseDatabase;
use app\models\mappers\CourseMapper;

/**
 * Course model implementing CourseRepositoryInterface.
 *
 * Handles persistence for the Course entity using MY_Model pure data CRUD engine
 * and delegates entity-database mapping to CourseMapper and CourseDatabase DTO.
 */
class Course_model extends MY_Model implements CourseRepositoryInterface
{
	/**
	 * Table name.
	 *
	 * @var string
	 */
	protected string $table = 'courses';

	/**
	 * Constructor.
	 */
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * Find a course by its ID.
	 *
	 * @param int $id Course ID
	 * @return Course|null
	 */
	public function find_by_id(int $id): ?Course
	{
		$row = $this->db->select('courses.*, categories.name as category_name')
			->from($this->table)
			->join('categories', 'categories.id = courses.category_id', 'left')
			->where('courses.id', $id)
			->where('courses.deleted_at IS NULL', null, false)
			->get()
			->row_array();

		if ($row === null) {
			return null;
		}

		return CourseMapper::to_entity(new CourseDatabase($row));
	}

	/**
	 * Find a course by its slug.
	 *
	 * @param CourseSlug $slug
	 * @return Course|null
	 */
	public function find_by_slug(CourseSlug $slug): ?Course
	{
		$row = $this->db->select('courses.*, categories.name as category_name')
			->from($this->table)
			->join('categories', 'categories.id = courses.category_id', 'left')
			->where('courses.slug', (string) $slug)
			->where('courses.deleted_at IS NULL', null, false)
			->get()
			->row_array();

		if ($row === null) {
			return null;
		}

		return CourseMapper::to_entity(new CourseDatabase($row));
	}

	/**
	 * Find all non-deleted courses.
	 *
	 * @return array<Course>
	 */
	public function find_all(): array
	{
		$rows = $this->db->select('courses.*, categories.name as category_name')
			->from($this->table)
			->join('categories', 'categories.id = courses.category_id', 'left')
			->where('courses.deleted_at IS NULL', null, false)
			->order_by('courses.title', 'ASC')
			->get()
			->result_array();

		return CourseMapper::to_entities($rows);
	}

	/**
	 * Find courses with server-side pagination, search, and filters.
	 *
	 * @param int $start Offset
	 * @param int $length Page size
	 * @param string $search Global search term
	 * @param string $order_col Column name
	 * @param string $order_dir ASC or DESC
	 * @param int|null $category_id Category filter
	 * @param string|null $status Status filter
	 * @return array{data: array<Course>, recordsFiltered: int}
	 */
	public function find_paginated(
		int $start,
		int $length,
		string $search,
		string $order_col,
		string $order_dir,
		?int $category_id = null,
		?string $status = null
	): array
	{
		$total_filtered = $this->_count_paginated($search, $category_id, $status);

		$allowed = ['id', 'title', 'category_id', 'status', 'workload_in_hours', 'created_at'];
		$col = in_array($order_col, $allowed, true) ? 'courses.' . $order_col : 'courses.title';
		$dir = strtoupper($order_dir) === 'ASC' ? 'ASC' : 'DESC';

		$this->db->select('courses.*, categories.name as category_name')
			->from($this->table)
			->join('categories', 'categories.id = courses.category_id', 'left')
			->where('courses.deleted_at IS NULL', null, false)
			->order_by($col, $dir)
			->limit($length, $start);

		if ($category_id !== null && $category_id > 0) {
			$this->db->where('courses.category_id', $category_id);
		}

		if ($status !== null && $status !== '') {
			$this->db->where('courses.status', $status);
		}

		if ($search !== '') {
			$this->db->group_start()
				->like('courses.title', $search)
				->or_like('courses.slug', $search)
				->or_like('categories.name', $search)
				->group_end();
		}

		$rows = $this->db->get()->result_array();

		return [
			'data' => CourseMapper::to_entities($rows),
			'recordsFiltered' => $total_filtered,
		];
	}

	/**
	 * Count total non-deleted courses.
	 *
	 * @return int
	 */
	public function count_all(): int
	{
		return $this->db
			->where('courses.deleted_at IS NULL', null, false)
			->from($this->table)
			->count_all_results();
	}

	/**
	 * Count non-deleted courses associated with a specific category.
	 *
	 * @param int $category_id
	 * @return int
	 */
	public function count_by_category_id(int $category_id): int
	{
		return $this->db
			->where('courses.category_id', $category_id)
			->where('courses.deleted_at IS NULL', null, false)
			->from($this->table)
			->count_all_results();
	}

	/**
	 * Create a new course record.
	 *
	 * @param Course $course
	 * @return Course
	 */
	public function create(Course $course): Course
	{
		$data = CourseMapper::to_database_create($course);
		$insert_id = (int) $this->insert($data);

		return $this->find_by_id($insert_id) ?? $course;
	}

	/**
	 * Save (insert or update) a course.
	 *
	 * @param Course $course
	 * @return Course
	 */
	public function save(Course $course): Course
	{
		if ($course->get_id() === null) {
			return $this->create($course);
		}

		$data = CourseMapper::to_database_update($course);
		$this->update($data, [$this->primary_key => $course->get_id()]);

		return $this->find_by_id($course->get_id()) ?? $course;
	}

	/**
	 * Delete a course (soft delete).
	 *
	 * @param Course $course
	 * @return Course
	 */
	public function delete(Course $course): Course
	{
		$course->delete();
		return $this->save($course);
	}

	/**
	 * Count filtered courses for DataTables pagination.
	 *
	 * @param string $search Global search term
	 * @param int|null $category_id Category ID
	 * @param string|null $status Status
	 * @return int
	 */
	private function _count_paginated(string $search, ?int $category_id = null, ?string $status = null): int
	{
		$this->db->from($this->table)
			->join('categories', 'categories.id = courses.category_id', 'left')
			->where('courses.deleted_at IS NULL', null, false);

		if ($category_id !== null && $category_id > 0) {
			$this->db->where('courses.category_id', $category_id);
		}

		if ($status !== null && $status !== '') {
			$this->db->where('courses.status', $status);
		}

		if ($search !== '') {
			$this->db->group_start()
				->like('courses.title', $search)
				->or_like('courses.slug', $search)
				->or_like('categories.name', $search)
				->group_end();
		}

		return $this->db->count_all_results();
	}
}
