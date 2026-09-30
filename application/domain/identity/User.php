<?php

namespace app\domain\identity;

use app\domain\identity\value_objects\Email;
use app\domain\identity\value_objects\Password;
use DateTime;

/**
 * Entity representing a system user.
 *
 * Handles identity creation, password verification,
 * activation state, and soft delete lifecycle.
 */
final class User
{
	/**
	 * Unique identifier.
	 *
	 * @var int|null
	 */
	private ?int $id = null;

	/**
	 * User's full name.
	 *
	 * @var string
	 */
	private string $name;

	/**
	 * User's email address (Value Object).
	 *
	 * @var Email
	 */
	private Email $email;

	/**
	 * User's password (Value Object).
	 *
	 * @var Password
	 */
	private Password $password;

	/**
	 * Whether the user account is active.
	 *
	 * @var bool
	 */
	private bool $is_active = true;

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
	 * Soft delete timestamp.
	 *
	 * @var DateTime|null
	 */
	private ?DateTime $deleted_at = null;

	/**
	 * Primary role slug.
	 *
	 * @var string|null
	 */
	private ?string $role = null;

	/**
	 * Create a user domain entity.
	 *
	 * @param string $name User's name
	 * @param Email $email User's email (Value Object)
	 * @param Password|string $password Plain text password or Password Value Object
	 * @param int|null $id User ID
	 * @param bool $is_active Active status
	 * @param DateTime|null $created_at Creation timestamp
	 * @param DateTime|null $updated_at Update timestamp
	 * @param DateTime|null $deleted_at Deletion timestamp
	 * @param string|null $role Primary role slug
	 * @return self
	 */
	public static function create(
		string $name,
		Email $email,
		Password|string $password,
		?int $id = null,
		bool $is_active = true,
		?DateTime $created_at = null,
		?DateTime $updated_at = null,
		?DateTime $deleted_at = null,
		?string $role = null
	): self
	{
		$user = new self();
		$user->id = $id;
		$user->name = $name;
		$user->email = $email;
		$user->password = $password instanceof Password ? $password : new Password($password);
		$user->is_active = $is_active;
		$user->created_at = $created_at ?? new DateTime();
		$user->updated_at = $updated_at ?? ($id === null ? new DateTime() : null);
		$user->deleted_at = $deleted_at;
		$user->role = $role;

		return $user;
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
	 * Get the password Value Object.
	 *
	 * @return Password
	 */
	public function get_password(): Password
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
	 * Activate the user.
	 *
	 * @return void
	 */
	public function activate(): void
	{
		$this->is_active = true;
	}

	/**
	 * Inactivate the user
	 *
	 * @return void
	 */
	public function inactivate(): void
	{
		$this->is_active = false;
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
	 * Get the soft delete timestamp.
	 *
	 * @return DateTime|null
	 */
	public function get_deleted_at(): ?DateTime
	{
		return $this->deleted_at;
	}

	/**
	 * Change the user password.
	 *
	 * @param Password|string $new_password Plain text password or Password Value Object
	 * @return void
	 */
	public function change_password(Password|string $new_password): void
	{
		$this->password = $new_password instanceof Password ? $new_password : new Password($new_password);
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
		$this->deleted_at = new DateTime();
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
