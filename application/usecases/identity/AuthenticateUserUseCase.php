<?php

namespace app\usecases\identity;

use app\domain\identity\Email;
use app\domain\identity\User;
use app\factories\Model_factory;

/**
 * Use case for authenticating a user in the system.
 *
 * Validates credentials and checks for deactivated accounts.
 * Accepts an optional repository for dependency injection (testing).
 */
class AuthenticateUserUseCase
{
    /** @var \app\domain\identity\UserRepositoryInterface */
    private $user_repository;

    /**
     * Constructor.
     *
     * @param \app\domain\identity\UserRepositoryInterface|null $repository Repository for testing (optional)
     */
    public function __construct($repository = null)
    {
        if ($repository !== null) {
            $this->user_repository = $repository;
        } else {
            $this->user_repository = Model_factory::make('User_model');
        }
    }

    /**
     * Execute authentication.
     *
     * Finds the user by email, validates the account is active,
     * and verifies the password.
     *
     * @param string $email User email
     * @param string $password Plain text password
     * @return User Authenticated user
     * @throws \RuntimeException When credentials are invalid or account is deactivated
     */
    public function execute(string $email, string $password): User
    {
        $user = $this->user_repository->find_by_email(new Email($email));

        if ($user === null) {
            throw new \RuntimeException("Invalid credentials");
        }

        if ($user->is_deleted()) {
            throw new \RuntimeException("Deactivated account");
        }

        if (!$user->verify_password($password)) {
            throw new \RuntimeException("Invalid credentials");
        }

        return $user;
    }
}
