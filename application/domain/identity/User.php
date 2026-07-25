<?php

namespace app\domain\identity;

/**
 * Entity representing a system user.
 *
 * Handles user creation, hydration from database, password verification,
 * role checks (admin/student), and soft delete lifecycle.
 */
class User
{
    private $id;
    private $name;
    private $email;
    private $password;
    private $role;
    private $created_at;
    private $updated_at;
    private $deleted_at;

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
     * @param array $row Database record with keys: id, name, email, password, role, created_at, updated_at, deleted_at
     * @return self
     */
    public static function from_database(array $row): self
    {
        $user = new self();
        $user->id = (int) $row['id'];
        $user->name = $row['name'];
        $user->email = new Email($row['email']);
        $user->password = $row['password'];
        $user->role = $row['role'] ?? null;
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
     * Get the user name.
     *
     * @return string
     */
    public function get_name(): string
    {
        return $this->name;
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
     * Get the hashed password.
     *
     * @return string
     */
    public function get_password(): string
    {
        return $this->password;
    }

    /**
     * Get the user role.
     *
     * @return string|null
     */
    public function get_role(): ?string
    {
        return $this->role;
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
}
