<?php

namespace tests\unit\mocks\repositories;

use app\domain\identity\Email;
use app\domain\identity\User;
use app\domain\identity\UserRepositoryInterface;

/**
 * Shared Mock repository for User UseCases testing.
 *
 * Stores users in-memory and provides find_by_id, find_by_email,
 * save, and delete operations for test isolation.
 */
class MockUserRepository implements UserRepositoryInterface
{
    public array $users = [];

    /**
     * Set the internal users array.
     *
     * @param array $users List of User entities
     * @return void
     */
    public function set_users(array $users)
    {
        $this->users = $users;
    }

    /**
     * Find a user by ID.
     *
     * @param int|string $id User ID
     * @return User|null
     */
    public function find_by_id(int|string $id): ?User
    {
        $id = (int) $id;
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
     * Save (add or update) a user to the in-memory list.
     *
     * @param User $user User entity
     * @return User
     */
    public function save(User $user): User
    {
        if ($user->get_id() === null) {
            $user->set_id(count($this->users) + 1);
            $this->users[] = $user;
        } else {
            foreach ($this->users as $index => $item) {
                if ($item->get_id() === $user->get_id()) {
                    $this->users[$index] = $user;
                    return $user;
                }
            }
        }

        return $user;
    }

    /**
     * Delete users matching conditions.
     *
     * @param array $where Filter conditions
     * @return bool
     */
    public function delete(array $where): bool
    {
        $id = $where['id'] ?? null;
        if ($id !== null) {
            $this->users = array_values(array_filter($this->users, function ($user) use ($id) {
                return $user->get_id() !== (int) $id;
            }));
        }
        return true;
    }

    /**
     * Return all users in the in-memory list.
     *
     * @return array User entities
     */
    public function find_all(): array
    {
        return $this->users;
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
            if ($user->has_role($role) && !$user->is_deleted()) {
                $count++;
            }
        }
        return $count;
    }
}
