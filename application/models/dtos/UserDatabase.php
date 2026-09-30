<?php

namespace app\models\dtos;

use DateTime;

/**
 * Database DTO representing a raw user record.
 */
final class UserDatabase
{
	public int $id;
	public string $name;
	public string $email;
	public string $password;
	public int $is_active;
	public ?string $role;
	public DateTime $created_at;
	public ?DateTime $updated_at;
	public ?DateTime $deleted_at;

	/**
	 * Constructor.
	 *
	 * @param array{id: int|string, name: string, email: string, password: string, is_active: int|string|bool, role?: ?string, created_at?: ?string, updated_at?: ?string, deleted_at?: ?string} $data
	 */
	public function __construct(array $data)
	{
		$this->id = (int) $data['id'];
		$this->name = (string) $data['name'];
		$this->email = (string) $data['email'];
		$this->password = (string) $data['password'];
		$this->is_active = !empty($data['is_active']) ? 1 : 0;
		$this->role = !empty($data['role']) ? (string) $data['role'] : null;
		$this->created_at = !empty($data['created_at']) ? new DateTime($data['created_at']) : new DateTime();
		$this->updated_at = !empty($data['updated_at']) ? new DateTime($data['updated_at']) : null;
		$this->deleted_at = !empty($data['deleted_at']) ? new DateTime($data['deleted_at']) : null;
	}
}
