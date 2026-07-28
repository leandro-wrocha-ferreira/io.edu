<?php

namespace app\usecases\admin;

use app\factories\Model_factory;

/**
 * Use case for soft-deleting a user.
 *
 * Sets deleted_at so the user no longer appears anywhere in the system.
 */
class DeleteUserUseCase
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
     * Soft-delete the user by setting deleted_at.
     *
     * @param int $user_id User ID
     * @return void
     * @throws \RuntimeException When user not found
     */
    public function execute(int $user_id): void
    {
        $user = $this->user_repository->find_by_id($user_id);
        if ($user === null) {
            throw new \RuntimeException("User not found");
        }

        $user->delete();
        $this->user_repository->save($user);
    }
}
