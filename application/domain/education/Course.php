<?php

namespace app\domain\education;

/**
 * Course Entity (Root aggregate for the curriculum).
 */
class Course
{
	private ?int $id = null;
	private ?int $category_id = null;
	private string $title;
	private ?string $description = null;
	private float $base_price = 0.00;

	/**
	 * Create a new Course.
	 *
	 * @param string $title Course title
	 * @param float $base_price Base price
	 * @param int|null $category_id Category ID
	 * @return self
	 */
	public static function create(string $title, float $base_price = 0.00, ?int $category_id = null): self
	{
		$course = new self();
		$course->title = $title;
		$course->base_price = $base_price;
		$course->category_id = $category_id;
		return $course;
	}

	/**
	 * Hydrate a Course from a database record.
	 *
	 * @param array $row Database row
	 * @return self
	 */
	public static function from_database(array $row): self
	{
		$course = new self();
		$course->id = (int) $row['id'];
		$course->category_id = isset($row['category_id']) ? (int) $row['category_id'] : null;
		$course->title = $row['title'];
		$course->description = $row['description'] ?? null;
		$course->base_price = (float) $row['base_price'];
		return $course;
	}

	/**
	 * Get Course ID.
	 *
	 * @return int|null
	 */
	public function get_id(): ?int
	{
		return $this->id;
	}

	/**
	 * Set Course ID.
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
	 * Get Category ID.
	 *
	 * @return int|null
	 */
	public function get_category_id(): ?int
	{
		return $this->category_id;
	}

	/**
	 * Set Category ID.
	 *
	 * @param int|null $category_id
	 * @return self
	 */
	public function set_category_id(?int $category_id): self
	{
		$this->category_id = $category_id;
		return $this;
	}

	/**
	 * Get Course title.
	 *
	 * @return string
	 */
	public function get_title(): string
	{
		return $this->title;
	}

	/**
	 * Set Course title.
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
	 * Get Course description.
	 *
	 * @return string|null
	 */
	public function get_description(): ?string
	{
		return $this->description;
	}

	/**
	 * Set Course description.
	 *
	 * @param string|null $description
	 * @return self
	 */
	public function set_description(?string $description): self
	{
		$this->description = $description;
		return $this;
	}

	/**
	 * Get Course base price.
	 *
	 * @return float
	 */
	public function get_base_price(): float
	{
		return $this->base_price;
	}

	/**
	 * Set Course base price.
	 *
	 * @param float $base_price
	 * @return self
	 */
	public function set_base_price(float $base_price): self
	{
		$this->base_price = $base_price;
		return $this;
	}
}
