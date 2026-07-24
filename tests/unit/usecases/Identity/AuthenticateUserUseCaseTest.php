<?php

use app\domain\identity\Email;
use app\domain\identity\User;
use app\domain\identity\UserRepositoryInterface;
use app\usecases\identity\AuthenticateUserUseCase;

class MockUserRepository implements UserRepositoryInterface
{
    private $users = [];

    public function set_users(array $users)
    {
        $this->users = $users;
    }

    public function find_by_id(int $id): ?User
    {
        foreach ($this->users as $user) {
            if ($user->get_id() === $id) {
                return $user;
            }
        }
        return null;
    }

    public function find_by_email(Email $email): ?User
    {
        foreach ($this->users as $user) {
            if ($user->get_email()->equals($email)) {
                return $user;
            }
        }
        return null;
    }

    public function save(User $user): void
    {
        $this->users[] = $user;
    }

    public function delete(int $id): void
    {
        $this->users = array_filter($this->users, function ($user) use ($id) {
            return $user->get_id() !== $id;
        });
    }
}

class AuthenticateUserUseCaseTest extends \PHPUnit\Framework\TestCase
{
    private $mock_repository;

    protected function setUp(): void
    {
        $this->mock_repository = new MockUserRepository();
    }

    public function test_authenticate_with_valid_credentials()
    {
        $email = new Email('john@example.com');
        $user = User::create('John', $email, 'password123');

        $this->mock_repository->set_users([$user]);

        $use_case = new AuthenticateUserUseCase($this->mock_repository);

        $result = $use_case->execute('john@example.com', 'password123');

        $this->assertEquals('John', $result->get_name());
    }

    public function test_authenticate_with_invalid_email_throws_exception()
    {
        $this->mock_repository->set_users([]);

        $use_case = new AuthenticateUserUseCase($this->mock_repository);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Credenciais inválidas');
        $use_case->execute('notfound@example.com', 'password123');
    }

    public function test_authenticate_with_invalid_password_throws_exception()
    {
        $email = new Email('john@example.com');
        $user = User::create('John', $email, 'correctpassword');

        $this->mock_repository->set_users([$user]);

        $use_case = new AuthenticateUserUseCase($this->mock_repository);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Credenciais inválidas');
        $use_case->execute('john@example.com', 'wrongpassword');
    }
}
