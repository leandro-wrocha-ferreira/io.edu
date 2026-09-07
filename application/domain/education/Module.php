<?php

namespace app\domain\education;

/**
 * Module Entity representing a course module.
 */
class Module
{
	private ?int $id = null;
	private int $course_id;
	private string $title;
	private int $sort_order = 0;

	/**
	 * Create a new Module.
	 *
	 * @param int $course_id Course ID
	 * @param string $title Module title
	 * @param int $sort_order Sort order
	 * @return self
	 */
	public static function create(int $course_id, string $title, int $sort_order = 0): self
	{
		$module = new self();
		$module->course_id = $course_id;
		$module->title = $title;
		$module->sort_order = $sort_order;
		return $module;
	}

	/**
	 * Hydrate a Module from a database row.
	 *
	 * @param array $row Database row
	 * @return self
	 */
	public static function from_database(array $row): self
	{
		$module = new self();
		$module->id = (int) $row['id'];
		$module->course_id = (int) $row['course_id'];
		$module->title = $row['title'];
		$module->sort_order = (int) $row['sort_order'];
		return $module;
	}

	/**
	 * Get module ID.
	 *
	 * @return int|null
	 */
	public function get_id(): ?int
	{
		return $this->id;
	}

	/**
	 * Set module ID.
	 *
	 * @param int $id
	 * @return self
	 */
	public function set_id(int $id): self
	{
		$this->id = $id;
		return $this;
	}

	/**
	 * Get course ID.
	 *
	 * @return int
	 */
	public function get_course_id(): int
	{
		return $this->course_id;
	}

	/**
	 * Set course ID.
	 *
	 * @param int $course_id
	 * @return self
	 */
	public function set_course_id(int $course_id): self
	{
		$this->course_id = $course_id;
		return $this;
	}

	/**
	 * Get module title.
	 *
	 * @return string
	 */
	public function get_title(): string
	{
		return $this->title;
	}

	/**
	 * Set module title.
	 *
	 * @param string $title
	 * @return self
	 */
	public function set_title(string $title): self
	{
		$this->title = $title;
		return $this;
	}

	/**
	 * Get sort order.
	 *
	 * @return int
	 */
	public function get_sort_order(): int
	{
		return $this->sort_order;
	}

	/**
	 * Set sort order.
	 *
	 * @param int $sort_order
	 * @return self
	 */
	public function set_sort_order(int $sort_order): self
	{
		$this->sort_order = $sort_order;
		return $this;
	}
}
