<?php

namespace app\domain\identity\repositories;

use app\domain\identity\User;
use app\domain\identity\value_objects\Email;

/**
 * Repository interface for User persistence.
 *
 * Defines the contract for storing and retrieving User entities.
 * Implementations handle the actual database interaction.
 */
interface UserRepositoryInterface
{
	/**
	 * Find a user by their ID.
	 *
	 * @param int $id User ID
	 * @return User|null User entity or null if not found
	 */
	public function find_by_id(int $id): ?User;

	/**
	 * Find a user by their email address.
	 *
	 * @param Email $email User email (Value Object)
	 * @return User|null User entity or null if not found
	 */
	public function find_by_email(Email $email): ?User;

	/**
	 * Create a new user.
	 *
	 * @param User $user User entity to persist
	 * @return User
	 */
	public function create(User $user): User;

	/**
	 * Save (insert or update) a user.
	 *
	 * @param User $user User entity to persist
	 * @return User
	 */
	public function save(User $user): User;

	/**
	 * Soft delete a user entity.
	 *
	 * @param User $user User entity to delete
	 * @return User
	 */
	public function delete(User $user): User;

	/**
	 * Find all non-deleted users, ordered by creation date DESC.
	 *
	 * @return array<User> User entities
	 */
	public function find_all(): array;

	/**
	 * Count non-deleted users by role slug.
	 *
	 * @param string $role Role slug (e.g. 'student')
	 * @return int
	 */
	public function count_by_role(string $role): int;

	/**
	 * Count all non-deleted users.
	 *
	 * @return int
	 */
	public function count_all(): int;

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
	public function find_paginated(int $start, int $length, string $search, string $order_col, string $order_dir): array;

	/**
	 * Sync assigned roles for a user in the user_roles association table.
	 *
	 * @param int $user_id User ID
	 * @param array<int> $role_ids Role IDs to associate
	 * @return void
	 */
	public function sync_user_roles(int $user_id, array $role_ids): void;

	/**
	 * Find all role slugs assigned to a user ID.
	 *
	 * @param int $user_id User ID
	 * @return array<string> List of role slugs (e.g. ['admin', 'student'])
	 */
	public function find_role_slugs_by_user_id(int $user_id): array;

	/**
	 * Find all role IDs assigned to a user ID.
	 *
	 * @param int $user_id User ID
	 * @return array<int> List of role IDs
	 */
	public function find_role_ids_by_user_id(int $user_id): array;
}
