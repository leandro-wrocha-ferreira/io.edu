<?php

namespace app\domain\admin;

/**
 * Entity representing a user role for RBAC.
 */
class Role
{
    private ?int $id;
    private string $name;
    private string $slug;
    private ?string $description;
    private array $permission_ids = [];
    private ?\DateTime $created_at = null;

    /**
     * Create a new Role.
     *
     * @param string $name Display name
     * @param string $slug Unique slug
     * @param string|null $description Optional description
     * @return self
     */
    public static function create(string $name, string $slug, ?string $description = null): self
    {
        $role = new self();
        $role->name = $name;
        $role->slug = $slug;
        $role->description = $description;
        $role->created_at = new \DateTime();
        return $role;
    }

    /**
     * Hydrate a Role from a database row.
     *
     * @param array $row Database record
     * @return self
     */
    public static function from_database(array $row): self
    {
        $role = new self();
        $role->id = (int) $row['id'];
        $role->name = $row['name'];
        $role->slug = $row['slug'];
        $role->description = $row['description'] ?? null;
        $role->permission_ids = isset($row['permission_ids'])
            ? (is_array($row['permission_ids']) ? $row['permission_ids'] : explode(',', $row['permission_ids']))
            : [];
        $role->created_at = isset($row['created_at']) ? new \DateTime($row['created_at']) : null;
        return $role;
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

    /**
     * @return array
     */
    public function get_permission_ids(): array
    {
        return $this->permission_ids;
    }

    /**
     * @param array $permission_ids
     * @return void
     */
    public function set_permission_ids(array $permission_ids): void
    {
        $this->permission_ids = $permission_ids;
    }

    /**
     * @return \DateTime|null
     */
    public function get_created_at(): ?\DateTime
    {
        return $this->created_at;
    }
}
