<?php

namespace app\domain\education;

/**
 * Section Entity representing a section inside a module.
 */
class Section
{
	private ?int $id = null;
	private int $module_id;
	private string $title;
	private int $sort_order = 0;

	/**
	 * Create a new Section.
	 *
	 * @param int $module_id Module ID
	 * @param string $title Section title
	 * @param int $sort_order Sort order
	 * @return self
	 */
	public static function create(int $module_id, string $title, int $sort_order = 0): self
	{
		$section = new self();
		$section->module_id = $module_id;
		$section->title = $title;
		$section->sort_order = $sort_order;
		return $section;
	}

	/**
	 * Hydrate a Section from a database row.
	 *
	 * @param array $row Database row
	 * @return self
	 */
	public static function from_database(array $row): self
	{
		$section = new self();
		$section->id = (int) $row['id'];
		$section->module_id = (int) $row['module_id'];
		$section->title = $row['title'];
		$section->sort_order = (int) $row['sort_order'];
		return $section;
	}

	/**
	 * Get section ID.
	 *
	 * @return int|null
	 */
	public function get_id(): ?int
	{
		return $this->id;
	}

	/**
	 * Set section ID.
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
	 * Get module ID.
	 *
	 * @return int
	 */
	public function get_module_id(): int
	{
		return $this->module_id;
	}

	/**
	 * Set module ID.
	 *
	 * @param int $module_id
	 * @return self
	 */
	public function set_module_id(int $module_id): self
	{
		$this->module_id = $module_id;
		return $this;
	}

	/**
	 * Get section title.
	 *
	 * @return string
	 */
	public function get_title(): string
	{
		return $this->title;
	}

	/**
	 * Set section title.
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
