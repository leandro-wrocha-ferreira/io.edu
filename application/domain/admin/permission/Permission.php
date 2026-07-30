<?php

namespace app\domain\admin\permission;

/**
 * Entity representing a permission for RBAC.
 */
class Permission
{
	private ?int $id;
	private string $name;
	private string $slug;
	private ?string $description;

	/**
	 * Create a new Permission.
	 *
	 * @param string $name Display name
	 * @param string $slug Unique slug
	 * @param string|null $description Optional description
	 * @return self
	 */
	public static function create(string $name, string $slug, ?string $description = null): self
	{
		$permission = new self();
		$permission->name = $name;
		$permission->slug = $slug;
		$permission->description = $description;
		return $permission;
	}

	/**
	 * Hydrate a Permission from a database row.
	 *
	 * @param array $row Database record
	 * @return self
	 */
	public static function from_database(array $row): self
	{
		$permission = new self();
		$permission->id = (int) $row['id'];
		$permission->name = $row['name'];
		$permission->slug = $row['slug'];
		$permission->description = $row['description'] ?? null;
		return $permission;
	}

	/**
	 * @return int|null
	 */
	public function get_id(): ?int
	{
		return $this->id;
	}

	/**
	 * @return string
	 */
	public function get_name(): string
	{
		return $this->name;
	}

	/**
	 * @param string $name
	 * @return void
	 */
	public function set_name(string $name): void
	{
		$this->name = $name;
	}

	/**
	 * @return string
	 */
	public function get_slug(): string
	{
		return $this->slug;
	}

	/**
	 * @param string $slug
	 * @return void
	 */
	public function set_slug(string $slug): void
	{
		$this->slug = $slug;
	}

	/**
	 * @return string|null
	 */
	public function get_description(): ?string
	{
		return $this->description;
	}

	/**
	 * @param string|null $description
	 * @return void
	 */
	public function set_description(?string $description): void
	{
		$this->description = $description;
	}
}
