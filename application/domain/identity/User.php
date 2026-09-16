<?php

namespace app\domain\identity;

use DateTime;

/**
 * Entity representing a system user.
 *
 * Handles user creation, hydration from database, password verification,
 * role checks (admin/student), and soft delete lifecycle.
 */
class User
{
    private ?int $id = null;
    private string $name;
    private Email $email;
    private string $password;
    private bool $is_active = true;
    private array $role_ids = [];
    private ?string $role = null;
    private ?DateTime $created_at = null;
    private ?DateTime $updated_at = null;
    private ?DateTime $deleted_at = null;

    /**
     * Create a new user with a hashed password.
     *
     * @param string $name User's name
     * @param Email $email User's email (Value Object)
     * @param string $password Plain text password to hash
     * @return self
     */
    public static function create(string $name, Email $email, string $password): self
    {
        $user = new self();
        $user->name = $name;
        $user->email = $email;
        $user->password = password_hash($password, PASSWORD_BCRYPT);
        $user->created_at = new \DateTime();
        
        return $user;
    }

    /**
     * Hydrate a User entity from a database row.
     *
     * Expects keys: id, name, email, password, role, role_ids,
     * created_at, updated_at, deleted_at.
     *
     * @param array $row Database record
     * @return self
     */
    public static function from_database(array $row): self
    {
        $user = new self();
        $user->id = (int) $row['id'];
        $user->name = $row['name'];
        $user->email = new Email($row['email']);
        $user->password = $row['password'];
        $user->is_active = isset($row['is_active']) ? (bool) $row['is_active'] : true;
        $user->role = $row['role'] ?? null;
        $user->role_ids = isset($row['role_ids']) ? json_decode($row['role_ids'], true) ?? [] : [];
        $user->created_at = isset($row['created_at']) ? new \DateTime($row['created_at']) : null;
        $user->updated_at = isset($row['updated_at']) ? new \DateTime($row['updated_at']) : null;
        $user->deleted_at = isset($row['deleted_at']) ? new \DateTime($row['deleted_at']) : null;
        return $user;
    }

    /**
     * Get the user ID.
     *
     * @return int|null
     */
    public function get_id(): ?int
    {
        return $this->id;
    }

    /**
     * Set the user ID (used after insert).
     *
     * @param int $id
     * @return void
     */
    public function set_id(int $id): void
    {
        $this->id = $id;
    }

    /**
     * Get the user name.
     *
     * @return string
     */
    public function get_name(): string
    {
        return $this->name;
    }

    /**
     * Set the user name.
     *
     * @param string $name
     * @return void
     */
    public function set_name(string $name): void
    {
        $this->name = $name;
    }

    /**
     * Get the user email.
     *
     * @return Email
     */
    public function get_email(): Email
    {
        return $this->email;
    }

    /**
     * Set the user email.
     *
     * @param Email $email
     * @return void
     */
    public function set_email(Email $email): void
    {
        $this->email = $email;
    }

    /**
     * Get the hashed password.
     *
     * @return string
     */
    public function get_password(): string
    {
        return $this->password;
    }

    /**
     * Check if the user is active.
     *
     * @return bool
     */
    public function is_active(): bool
    {
        return $this->is_active;
    }

    /**
     * Set the active status.
     *
     * @param bool $active
     * @return void
     */
    public function set_active(bool $active): void
    {
        $this->is_active = $active;
    }

    /**
     * Get the primary role slug.
     *
     * @return string|null
     */
    public function get_role(): ?string
    {
        return $this->role;
    }

    /**
     * Set the primary role slug.
     *
     * @param string|null $role
     * @return void
     */
    public function set_role(?string $role): void
    {
        $this->role = $role;
    }

    /**
     * Get all role IDs assigned to the user.
     *
     * @return array
     */
    public function get_role_ids(): array
    {
        return $this->role_ids;
    }

    /**
     * Set the role IDs assigned to the user.
     *
     * @param array $role_ids
     * @return void
     */
    public function set_role_ids(array $role_ids): void
    {
        $this->role_ids = $role_ids;
    }

    /**
     * Check if the user has a specific role by slug.
     *
     * @param string $slug Role slug (e.g. 'admin', 'student')
     * @return bool
     */
    public function has_role(string $slug): bool
    {
        return $this->role === $slug;
    }

    /**
     * Get the creation timestamp.
     *
     * @return \DateTime|null
     */
    public function get_created_at(): ?\DateTime
    {
        return $this->created_at;
    }

    /**
     * Check if the user has admin-master role.
     *
     * @return bool
     */
    public function is_admin_master(): bool
    {
        return $this->role === 'admin-master';
    }

    /**
     * Check if the user has admin role.
     *
     * @return bool
     */
    public function is_admin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Check if the user has student role.
     *
     * @return bool
     */
    public function is_student(): bool
    {
        return $this->role === 'student';
    }

    /**
     * Verify a plain text password against the stored hash.
     *
     * @param string $password Plain text password
     * @return bool
     */
    public function verify_password(string $password): bool
    {
        return password_verify($password, $this->password);
    }

    /**
     * Change the user password.
     *
     * Hashes the new password and updates the timestamp.
     *
     * @param string $new_password New plain text password
     * @return void
     */
    public function change_password(string $new_password): void
    {
        $this->password = password_hash($new_password, PASSWORD_BCRYPT);
        $this->updated_at = new \DateTime();
    }

    /**
     * Check if the user is soft-deleted.
     *
     * @return bool
     */
    public function is_deleted(): bool
    {
        return $this->deleted_at !== null;
    }

    /**
     * Soft delete the user by setting the deleted_at timestamp.
     *
     * @return void
     */
    public function delete(): void
    {
        $this->deleted_at = new \DateTime();
    }

    /**
     * Restore a soft-deleted user.
     *
     * @return void
     */
    public function restore(): void
    {
        $this->deleted_at = null;
    }
}
