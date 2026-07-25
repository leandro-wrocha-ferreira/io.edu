<?php

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
     * Delete a user by ID from the in-memory list.
     *
     * @param int $id User ID
     * @return void
     */
    public function delete(int $id): void
    {
        $this->users = array_filter($this->users, function ($user) use ($id) {
            return $user->get_id() !== $id;
        });
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

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Invalid credentials');
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

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Invalid credentials');
        $use_case->execute('john@example.com', 'wrongpassword');
    }
}
