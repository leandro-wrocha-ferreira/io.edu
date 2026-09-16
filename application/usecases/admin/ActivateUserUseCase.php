<?php

namespace app\usecases\admin;

use app\domain\exceptions\NotFoundException;
use app\domain\identity\User;
use app\domain\identity\UserRepositoryInterface;
use app\factories\Model_factory;

/**
 * Use case for activating a user.
 */
class ActivateUserUseCase
{
    /** @var \app\domain\identity\UserRepositoryInterface */
    private $user_repository;

    /**
     * Constructor.
     *
     * @param \app\domain\identity\UserRepositoryInterface $user_repository
     */
    public function __construct(UserRepositoryInterface $user_repository)
    {
        $this->user_repository = $user_repository;
    }

    /**
     * Activate the user.
     *
     * @param int $user_id User ID
     * @return User
     * @throws NotFoundException When user not found
     */
    public function execute(int $user_id): User
    {
        $user = $this->user_repository->find_by_id($user_id);
        if ($user === null) {
            throw new NotFoundException("User not found");
        }

        $user->set_active(true);
        return $this->user_repository->save($user);
    }
}
