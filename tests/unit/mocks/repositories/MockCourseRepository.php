<?php

namespace tests\unit\mocks\repositories;

use app\domain\course\Course;
use app\domain\course\repositories\CourseRepositoryInterface;
use app\domain\course\value_objects\CourseSlug;
use app\models\dtos\CourseDatabase;
use app\models\mappers\CourseMapper;

/**
 * Shared mock repository for Course tests.
 *
 * Simulates raw database storage and validates DTO/Mapper transformations.
 */
class MockCourseRepository implements CourseRepositoryInterface
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
	 * Find a course by ID.
	 *
	 * @param int $id Course ID
	 * @return Course|null
	 */
	public function find_by_id(int $id): ?Course
	{
		foreach ($this->rows as $row) {
			if ($row['id'] === $id && empty($row['deleted_at'])) {
				return CourseMapper::to_entity(new CourseDatabase($row));
			}
		}
		return null;
	}

	/**
	 * Find a course by slug.
	 *
	 * @param CourseSlug $slug
	 * @return Course|null
	 */
	public function find_by_slug(CourseSlug $slug): ?Course
	{
		$slug_str = (string) $slug;
		foreach ($this->rows as $row) {
			if ($row['slug'] === $slug_str && empty($row['deleted_at'])) {
				return CourseMapper::to_entity(new CourseDatabase($row));
			}
		}
		return null;
	}

	/**
	 * Find all non-deleted courses.
	 *
	 * @return array<Course>
	 */
	public function find_all(): array
	{
		$active_rows = array_filter($this->rows, function (array $row) {
			return empty($row['deleted_at']);
		});

		return CourseMapper::to_entities(array_values($active_rows));
	}

	/**
	 * Find courses with server-side pagination, search, and filters.
	 *
	 * @param int $start
	 * @param int $length
	 * @param string $search
	 * @param string $order_col
	 * @param string $order_dir
	 * @param int|null $category_id
	 * @param string|null $status
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
		$filtered_rows = array_filter($this->rows, function (array $row) use ($search, $category_id, $status) {
			if (!empty($row['deleted_at'])) {
				return false;
			}
			if ($category_id !== null && $category_id > 0 && (int) $row['category_id'] !== $category_id) {
				return false;
			}
			if ($status !== null && $status !== '' && $row['status'] !== $status) {
				return false;
			}
			if ($search !== '') {
				$title_match = stripos((string) $row['title'], $search) !== false;
				$slug_match = stripos((string) $row['slug'], $search) !== false;
				return $title_match || $slug_match;
			}
			return true;
		});

		$total_filtered = count($filtered_rows);
		$sliced = array_slice(array_values($filtered_rows), $start, $length);

		return [
			'data' => CourseMapper::to_entities($sliced),
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
		$active_rows = array_filter($this->rows, function (array $row) {
			return empty($row['deleted_at']);
		});

		return count($active_rows);
	}

	/**
	 * Count non-deleted courses associated with a category.
	 *
	 * @param int $category_id
	 * @return int
	 */
	public function count_by_category_id(int $category_id): int
	{
		$count = 0;
		foreach ($this->rows as $row) {
			if ((int) $row['category_id'] === $category_id && empty($row['deleted_at'])) {
				$count++;
			}
		}
		return $count;
	}

	/**
	 * Create a new course.
	 *
	 * @param Course $course
	 * @return Course
	 */
	public function create(Course $course): Course
	{
		$data = CourseMapper::to_database_create($course);
		$data['id'] = $this->auto_increment++;
		$data['created_at'] = date('Y-m-d H:i:s');
		$data['updated_at'] = date('Y-m-d H:i:s');
		$data['deleted_at'] = null;
		$data['category_name'] = 'Categoria Teste';

		$this->rows[$data['id']] = $data;

		return CourseMapper::to_entity(new CourseDatabase($data));
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

		$course_id = $course->get_id();
		$update_data = CourseMapper::to_database_update($course);

		if (isset($this->rows[$course_id])) {
			$existing = $this->rows[$course_id];
			$merged = array_merge($existing, $update_data, [
				'updated_at' => date('Y-m-d H:i:s'),
			]);
			$this->rows[$course_id] = $merged;
			return CourseMapper::to_entity(new CourseDatabase($merged));
		}

		$update_data['id'] = $course_id;
		$update_data['created_at'] = date('Y-m-d H:i:s');
		$update_data['updated_at'] = date('Y-m-d H:i:s');
		$update_data['category_name'] = 'Categoria Teste';
		$this->rows[$course_id] = $update_data;

		return CourseMapper::to_entity(new CourseDatabase($update_data));
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
}
