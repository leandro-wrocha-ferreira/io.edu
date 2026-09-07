<?php

use app\domain\exceptions\UnauthorizedException;
use app\domain\identity\Email;
use app\domain\identity\User;
use app\domain\identity\UserRepositoryInterface;
use app\usecases\identity\AuthenticateUserUseCase;

/**
 * Mock repository for AuthenticateUserUseCase testing.
 *
 * Stores users in-memory and provides find_by_id, find_by_email,
 * save, and delete operations for test isolation.
 */
class MockUserRepository implements UserRepositoryInterface
{
    private $users = [];

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
    public function find_by_id($id): ?User
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
     * Save (add) a user to the in-memory list.
     *
     * @param User $user User entity
     * @return void
     */
    public function save(User $user): void
    {
        $this->users[] = $user;
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

    /**
     * Count users by role slug on a specific date.
     *
     * @param string $role Role slug
     * @param string $date Date string (Y-m-d)
     * @return int
     */
    public function count_by_role_and_date(string $role, string $date): int
    {
        $count = 0;
        foreach ($this->users as $user) {
            if (
                $user->has_role($role)
                && !$user->is_deleted()
                && $user->get_created_at() !== null
                && $user->get_created_at()->format('Y-m-d') === $date
            ) {
                $count++;
            }
        }
        return $count;
    }
}

/**
 * Test suite for AuthenticateUserUseCase.
 *
 * Covers successful authentication, invalid email, and invalid password scenarios.
 */
class AuthenticateUserUseCaseTest extends \PHPUnit\Framework\TestCase
{
    /** @var MockUserRepository */
    private $mock_repository;

    /**
     * Set up test environment.
     *
     * Creates a fresh MockUserRepository before each test.
     *
     * @return void
     */
    protected function setUp(): void
    {
        $this->mock_repository = new MockUserRepository();
    }

    /**
     * Test successful authentication with valid credentials.
     *
     * @return void
     */
    public function test_authenticate_with_valid_credentials()
    {
        $email = new Email('john@example.com');
        $user = User::create('John', $email, 'password123');

        $this->mock_repository->set_users([$user]);

        $use_case = new AuthenticateUserUseCase($this->mock_repository);

        $result = $use_case->execute('john@example.com', 'password123');

        $this->assertEquals('John', $result->get_name());
    }

    /**
     * Test that authentication throws exception for non-existing email.
     *
     * @return void
     */
    public function test_authenticate_with_invalid_email_throws_exception()
    {
        $this->mock_repository->set_users([]);

        $use_case = new AuthenticateUserUseCase($this->mock_repository);

        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessage('Credenciais inválidas');
        $use_case->execute('notfound@example.com', 'password123');
    }

    /**
     * Test that authentication throws exception for wrong password.
     *
     * @return void
     */
    public function test_authenticate_with_invalid_password_throws_exception()
    {
        $email = new Email('john@example.com');
        $user = User::create('John', $email, 'correctpassword');

        $this->mock_repository->set_users([$user]);

        $use_case = new AuthenticateUserUseCase($this->mock_repository);

        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessage('Credenciais inválidas');
        $use_case->execute('john@example.com', 'wrongpassword');
    }
}
