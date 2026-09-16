<?php

namespace app\domain\identity;

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
     * @param int|string $id User ID
     * @return User|null User entity or null if not found
     */
    public function find_by_id(int|string $id): ?User;

    /**
     * Find a user by their email address.
     *
     * @param Email $email User email (Value Object)
     * @return User|null User entity or null if not found
     */
    public function find_by_email(Email $email): ?User;

    /**
     * Save (insert or update) a user.
     *
     * @param User $user User entity to persist
     * @return User
     */
    public function save(User $user): User;

    /**
     * Soft delete users matching specified conditions.
     *
     * @param array $where Filter conditions (e.g. ['id' => $id])
     * @return bool
     */
    public function delete(array $where): bool;

    /**
     * Find all non-deleted users, ordered by creation date DESC.
     *
     * @return array User entities
     */
    public function find_all(): array;

    /**
     * Count non-deleted users by role slug.
     *
     * @param string $role Role slug (e.g. 'student')
     * @return int
     */
    public function count_by_role(string $role): int;
}
