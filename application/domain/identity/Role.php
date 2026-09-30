<?php

namespace app\domain\identity;

use DateTime;

/**
 * Entity representing a user role for identity.
 */
final class Role
{
	/**
	 * Unique identifier.
	 *
	 * @var int|null
	 */
	private ?int $id = null;

	/**
	 * Role display name.
	 *
	 * @var string
	 */
	private string $name;

	/**
	 * Role unique slug identifier.
	 *
	 * @var string
	 */
	private string $slug;

	/**
	 * Role description.
	 *
	 * @var string|null
	 */
	private ?string $description = null;

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
	 * Create a role domain entity.
	 *
	 * @param string $name Display name
	 * @param string $slug Unique slug
	 * @param string|null $description Optional description
	 * @param int|null $id Role ID
	 * @param DateTime|null $created_at Creation timestamp
	 * @param DateTime|null $updated_at Update timestamp
	 * @return self
	 */
	public static function create(
		string $name,
		string $slug,
		?string $description = null,
		?int $id = null,
		?DateTime $created_at = null,
		?DateTime $updated_at = null
	): self
	{
		$role = new self();
		$role->id = $id;
		$role->name = $name;
		$role->slug = $slug;
		$role->description = $description;
		$role->created_at = $created_at ?? new DateTime();
		$role->updated_at = $updated_at ?? ($id === null ? new DateTime() : null);

		return $role;
	}

	/**
	 * Get the role ID.
	 *
	 * @return int|null
	 */
	public function get_id(): ?int
	{
		return $this->id;
	}

	/**
	 * Get the display name.
	 *
	 * @return string
	 */
	public function get_name(): string
	{
		return $this->name;
	}

	/**
	 * Set the display name.
	 *
	 * @param string $name
	 * @return void
	 */
	public function set_name(string $name): void
	{
		$this->name = $name;
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
	 */
	public function set_slug(string $slug): void
	{
		$this->slug = $slug;
	}

	/**
	 * Get the description.
	 *
	 * @return string|null
	 */
	public function get_description(): ?string
	{
		return $this->description;
	}

	/**
	 * Set the description.
	 *
	 * @param string|null $description
	 * @return void
	 */
	public function set_description(?string $description): void
	{
		$this->description = $description;
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
}
