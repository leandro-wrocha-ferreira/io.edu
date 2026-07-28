<?php

namespace app\usecases\admin;

use app\domain\identity\Email;
use app\domain\identity\User;
use app\factories\Model_factory;

/**
 * Use case for creating a new user.
 */
class CreateUserUseCase
{
    /** @var \app\domain\identity\UserRepositoryInterface */
    private $user_repository;

    /** @var \app\domain\admin\RoleRepositoryInterface */
    private $role_repository;

    /**
     * Constructor.
     *
     * @param \app\domain\identity\UserRepositoryInterface|null $user_repo User repository for testing (optional)
     * @param \app\domain\admin\RoleRepositoryInterface|null $role_repo Role repository for testing (optional)
     */
    public function __construct($user_repo = null, $role_repo = null)
    {
        if ($user_repo !== null) {
            $this->user_repository = $user_repo;
        } else {
            $this->user_repository = Model_factory::make('user_model');
        }

        if ($role_repo !== null) {
            $this->role_repository = $role_repo;
        } else {
            $this->role_repository = Model_factory::make('role_model');
        }
    }

    /**
     * Execute the use case.
     *
     * @param string $name User's full name
     * @param string $email User's email address
     * @param string $password Plain text password
     * @param array $role_ids Role IDs to assign
     * @return User Created user entity
     * @throws \RuntimeException When email already exists or invalid role
     */
    public function execute(string $name, string $email, string $password, array $role_ids = []): User
    {
        $email_vo = new Email($email);

        $existing = $this->user_repository->find_by_email($email_vo);
        if ($existing !== null) {
            throw new \RuntimeException("Email already in use");
        }

        $user = User::create($name, $email_vo, $password);
        $user->set_role_ids($role_ids);

        $this->user_repository->save($user);

        return $user;
    }
}
