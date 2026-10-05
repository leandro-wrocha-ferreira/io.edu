<?php

namespace app\domain\course;

use app\domain\course\constants\CategoryStatus;
use DateTime;
use InvalidArgumentException;

/**
 * Domain entity representing a course category.
 */
final class Category
{
	/**
	 * Unique identifier.
	 *
	 * @var int|null
	 */
	private ?int $id = null;

	/**
	 * Category name.
	 *
	 * @var string
	 */
	private string $name;

	/**
	 * Category slug.
	 *
	 * @var string
	 */
	private string $slug;

	/**
	 * Status ('active' or 'inactive').
	 *
	 * @var string
	 */
	private string $status;

	/**
	 * Creation timestamp.
	 *
	 * @var DateTime|null
	 */
	private ?DateTime $created_at = null;

	/**
	 * Update timestamp.
	 *
	 * @var DateTime|null
	 */
	private ?DateTime $updated_at = null;

	/**
	 * Deletion timestamp (soft delete).
	 *
	 * @var DateTime|null
	 */
	private ?DateTime $deleted_at = null;

	/**
	 * Create a category domain entity.
	 *
	 * @param string $name Category name
	 * @param string $slug Unique slug
	 * @param string $status Status ('active' or 'inactive')
	 * @param int|null $id Category ID
	 * @param DateTime|null $created_at Creation timestamp
	 * @param DateTime|null $updated_at Update timestamp
	 * @param DateTime|null $deleted_at Deletion timestamp
	 * @return self
	 * @throws InvalidArgumentException When name or slug is empty, or status is invalid
	 */
	public static function create(
		string $name,
		string $slug,
		string $status = CategoryStatus::ACTIVE,
		?int $id = null,
		?DateTime $created_at = null,
		?DateTime $updated_at = null,
		?DateTime $deleted_at = null
	): self
	{
		$trimmed_name = trim($name);
		if ($trimmed_name === '') {
			throw new InvalidArgumentException("Category name cannot be empty");
		}

		$trimmed_slug = trim(strtolower($slug));
		if ($trimmed_slug === '') {
			throw new InvalidArgumentException("Category slug cannot be empty");
		}

		if (!CategoryStatus::is_valid($status)) {
			throw new InvalidArgumentException("Invalid category status: {$status}");
		}

		$category = new self();
		$category->id = $id;
		$category->name = $trimmed_name;
		$category->slug = $trimmed_slug;
		$category->status = $status;
		$category->created_at = $created_at ?? new DateTime();
		$category->updated_at = $updated_at ?? ($id === null ? new DateTime() : null);
		$category->deleted_at = $deleted_at;

		return $category;
	}

	/**
	 * Get the category ID.
	 *
	 * @return int|null
	 */
	public function get_id(): ?int
	{
		return $this->id;
	}

	/**
	 * Get the category name.
	 *
	 * @return string
	 */
	public function get_name(): string
	{
		return $this->name;
	}

	/**
	 * Set the category name.
	 *
	 * @param string $name
	 * @return void
	 * @throws InvalidArgumentException
	 */
	public function set_name(string $name): void
	{
		$trimmed = trim($name);
		if ($trimmed === '') {
			throw new InvalidArgumentException("Category name cannot be empty");
		}
		$this->name = $trimmed;
	}

	/**
	 * Get the unique slug.
	 *
	 * @return string
	 */
	public function get_slug(): string
	{
		return $this->slug;
	}

	/**
	 * Set the unique slug.
	 *
	 * @param string $slug
	 * @return void
	 * @throws InvalidArgumentException
	 */
	public function set_slug(string $slug): void
	{
		$trimmed = trim(strtolower($slug));
		if ($trimmed === '') {
			throw new InvalidArgumentException("Category slug cannot be empty");
		}
		$this->slug = $trimmed;
	}

	/**
	 * Get the status.
	 *
	 * @return string
	 */
	public function get_status(): string
	{
		return $this->status;
	}

	/**
	 * Set the status.
	 *
	 * @param string $status
	 * @return void
	 * @throws InvalidArgumentException
	 */
	public function set_status(string $status): void
	{
		if (!CategoryStatus::is_valid($status)) {
			throw new InvalidArgumentException("Invalid category status: {$status}");
		}
		$this->status = $status;
	}

	/**
	 * Check if the category is active.
	 *
	 * @return bool
	 */
	public function is_active(): bool
	{
		return $this->status === CategoryStatus::ACTIVE && !$this->is_deleted();
	}

	/**
	 * Activate the category.
	 *
	 * @return void
	 */
	public function activate(): void
	{
		$this->status = CategoryStatus::ACTIVE;
	}

	/**
	 * Deactivate the category.
	 *
	 * @return void
	 */
	public function deactivate(): void
	{
		$this->status = CategoryStatus::INACTIVE;
	}

	/**
	 * Mark the category as soft-deleted.
	 *
	 * @return void
	 */
	public function delete(): void
	{
		$this->deleted_at = new DateTime();
	}

	/**
	 * Check if the category is deleted.
	 *
	 * @return bool
	 */
	public function is_deleted(): bool
	{
		return $this->deleted_at !== null;
	}

	/**
	 * Get the creation timestamp.
	 *
	 * @return DateTime|null
	 */
	public function get_created_at(): ?DateTime
	{
		return $this->created_at;
	}

	/**
	 * Get the update timestamp.
	 *
	 * @return DateTime|null
	 */
	public function get_updated_at(): ?DateTime
	{
		return $this->updated_at;
	}

	/**
	 * Get the deletion timestamp.
	 *
	 * @return DateTime|null
	 */
	public function get_deleted_at(): ?DateTime
	{
		return $this->deleted_at;
	}
}
