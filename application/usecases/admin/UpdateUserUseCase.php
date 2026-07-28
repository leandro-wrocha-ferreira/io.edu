<?php

namespace app\usecases\admin;

use app\domain\identity\Email;
use app\domain\identity\User;
use app\factories\Model_factory;

/**
 * Use case for updating an existing user.
 *
 * Does not handle password changes — use a separate reset flow.
 */
class UpdateUserUseCase
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
            $this->user_repository = Model_factory::make('user_model');
        }
    }

    /**
     * Execute the use case.
     *
     * @param int $user_id User ID
     * @param string $name New name
     * @param string $email New email
     * @param array $role_ids Role IDs to assign
     * @return User Updated user entity
     * @throws \RuntimeException When user not found or email already in use
     */
    public function execute(int $user_id, string $name, string $email, array $role_ids = []): User
    {
        $user = $this->user_repository->find_by_id($user_id);
        if ($user === null) {
            throw new \RuntimeException("User not found");
        }

        $email_vo = new Email($email);

        $existing = $this->user_repository->find_by_email($email_vo);
        if ($existing !== null && $existing->get_id() !== $user_id) {
            throw new \RuntimeException("Email already in use");
        }

        $user->set_name($name);
        $user->set_email($email_vo);
        $user->set_role_ids($role_ids);

        $this->user_repository->save($user);

        return $user;
    }
}
