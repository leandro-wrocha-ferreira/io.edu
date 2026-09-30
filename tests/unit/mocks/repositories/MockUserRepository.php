<?php

namespace tests\unit\mocks\repositories;

use app\domain\identity\User;
use app\domain\identity\repositories\UserRepositoryInterface;
use app\domain\identity\value_objects\Email;

/**
 * Shared Mock repository for User UseCases testing.
 *
 * Stores users in-memory and provides standard repository
 * operations without fake setters or reflection.
 */
class MockUserRepository implements UserRepositoryInterface
{
	/** @var array<User> */
	private array $users = [];

	/** @var array<int, array<int>> Map of user_id => array<role_id> */
	private array $user_roles = [];

	/** @var array<int, string> Map of role_id => role_slug */
	private array $role_slug_map = [
		1 => 'admin-master',
		2 => 'admin',
		3 => 'student',
	];

	/**
	 * Find a user by ID.
	 *
	 * @param int $id User ID
	 * @return User|null
	 */
	public function find_by_id(int $id): ?User
	{
		foreach ($this->users as $user) {
			if ($user->get_id() === $id) {
				return $user;
			}
		}
		return null;
	}

	/**
	 * Find a user by email.
	 *
	 * @param Email $email User email
	 * @return User|null
	 */
	public function find_by_email(Email $email): ?User
	{
		foreach ($this->users as $user) {
			if ($user->get_email()->equals($email)) {
				return $user;
			}
		}
		return null;
	}

	/**
	 * Create a new user.
	 *
	 * @param User $user User entity
	 * @return User
	 */
	public function create(User $user): User
	{
		return $this->save($user);
	}

	/**
	 * Save (add or update) a user to the in-memory list.
	 *
	 * @param User $user User entity
	 * @return User
	 */
	public function save(User $user): User
	{
		if ($user->get_id() === null) {
			$new_id = count($this->users) + 1;
			$persisted = User::create(
				$user->get_name(),
				$user->get_email(),
				$user->get_password(),
				$new_id,
				$user->is_active(),
				$user->get_created_at(),
				$user->get_updated_at(),
				$user->get_deleted_at(),
				$user->get_role()
			);
			$this->users[] = $persisted;
			return $persisted;
		}

		foreach ($this->users as $index => $item) {
			if ($item->get_id() === $user->get_id()) {
				$this->users[$index] = $user;
				return $user;
			}
		}

		$this->users[] = $user;
		return $user;
	}

	/**
	 * Delete a user entity.
	 *
	 * @param User $user User entity to delete
	 * @return User
	 */
	public function delete(User $user): User
	{
		$user->delete();
		return $this->save($user);
	}

	/**
	 * Return all users in the in-memory list.
	 *
	 * @return array<User> User entities
	 */
	public function find_all(): array
	{
		return array_values(array_filter($this->users, function (User $user) {
			return !$user->is_deleted();
		}));
	}

	/**
	 * Count users by role slug.
	 *
	 * @param string $role Role slug
	 * @return int
	 */
	public function count_by_role(string $role): int
	{
		$count = 0;
		foreach ($this->users as $user) {
			if ($user->is_deleted()) {
				continue;
			}
			$slugs = $this->find_role_slugs_by_user_id($user->get_id());
			if (in_array($role, $slugs, true)) {
				$count++;
			}
		}
		return $count;
	}

	/**
	 * Count all non-deleted users.
	 *
	 * @return int
	 */
	public function count_all(): int
	{
		$active_users = array_filter($this->users, function (User $user) {
			return !$user->is_deleted();
		});
		return count($active_users);
	}

	/**
	 * Find users for server-side DataTables with search, order, and pagination.
	 *
	 * @param int $start Offset
	 * @param int $length Page size
	 * @param string $search Global search term
	 * @param string $order_col Column name to order by
	 * @param string $order_dir ASC or DESC
	 * @return array{data: array<User>, recordsFiltered: int}
	 */
	public function find_paginated(int $start, int $length, string $search, string $order_col, string $order_dir): array
	{
		$filtered = array_filter($this->users, function (User $user) use ($search) {
			if ($user->is_deleted()) {
				return false;
			}
			if ($search !== '') {
				$name_match = stripos($user->get_name(), $search) !== false;
				$email_match = stripos((string) $user->get_email(), $search) !== false;
				return $name_match || $email_match;
			}
			return true;
		});

		$total_filtered = count($filtered);
		$sliced = array_slice(array_values($filtered), $start, $length);

		return [
			'data' => $sliced,
			'recordsFiltered' => $total_filtered,
		];
	}

	/**
	 * Sync assigned roles for a user.
	 *
	 * @param int $user_id User ID
	 * @param array<int> $role_ids Role IDs
	 * @return void
	 */
	public function sync_user_roles(int $user_id, array $role_ids): void
	{
		$this->user_roles[$user_id] = array_values(array_map('intval', $role_ids));
	}

	/**
	 * Find all role slugs assigned to a user ID.
	 *
	 * @param int $user_id User ID
	 * @return array<string> List of role slugs
	 */
	public function find_role_slugs_by_user_id(int $user_id): array
	{
		$role_ids = $this->user_roles[$user_id] ?? [];
		$slugs = [];
		foreach ($role_ids as $role_id) {
			if (isset($this->role_slug_map[$role_id])) {
				$slugs[] = $this->role_slug_map[$role_id];
			}
		}
		return $slugs;
	}

	/**
	 * Find all role IDs assigned to a user ID.
	 *
	 * @param int $user_id User ID
	 * @return array<int> List of role IDs
	 */
	public function find_role_ids_by_user_id(int $user_id): array
	{
		return $this->user_roles[$user_id] ?? [];
	}
}
