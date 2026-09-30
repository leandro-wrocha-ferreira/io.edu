<?php

namespace app\models\mappers;

use app\domain\identity\User;
use app\domain\identity\value_objects\Email;
use app\domain\identity\value_objects\Password;
use app\models\dtos\UserDatabase;

/**
 * Mapper for converting between raw database data and User domain entity.
 */
class UserMapper
{
	/**
	 * Convert a raw database row into a User domain entity.
	 *
	 * @param UserDatabase $row Raw database row DTO
	 * @return User User domain entity
	 */
	public static function to_entity(UserDatabase $row): User
	{
		return User::create(
			$row->name,
			new Email($row->email),
			Password::from_hash($row->password),
			$row->id ?? null,
			(bool) $row->is_active,
			$row->created_at,
			$row->updated_at,
			$row->deleted_at ?? null,
			$row->role ?? null
		);
	}

	/**
	 * Convert multiple raw database rows into an array of User domain entities.
	 *
	 * @param array<array<string, mixed>> $rows
	 * @return array<User>
	 */
	public static function to_entities(array $rows): array
	{
		if (empty($rows)) {
			return [];
		}

		$entities = [];
		foreach ($rows as $row) {
			$entities[] = self::to_entity(new UserDatabase($row));
		}

		return $entities;
	}

	/**
	 * Map a User entity to a database array for record creation.
	 *
	 * @param User $user User entity
	 * @return array<string, mixed> Raw database columns map
	 */
	public static function to_database_create(User $user): array
	{
		return [
			'name' => $user->get_name(),
			'email' => (string) $user->get_email(),
			'password' => (string) $user->get_password(),
			'is_active' => $user->is_active() ? 1 : 0,
		];
	}

	/**
	 * Map a User entity to a database array for record update.
	 *
	 * @param User $user User entity
	 * @return array<string, mixed> Raw database columns map
	 */
	public static function to_database_update(User $user): array
	{
		$data = [
			'name' => $user->get_name(),
			'email' => (string) $user->get_email(),
			'password' => (string) $user->get_password(),
			'is_active' => $user->is_active() ? 1 : 0,
			'deleted_at' => $user->get_deleted_at() ? $user->get_deleted_at()->format('Y-m-d H:i:s') : null,
		];

		return $data;
	}
}