<?php

namespace app\usecases\admin;

use app\domain\identity\User;
use app\domain\exceptions\NotFoundException;
use app\factories\Model_factory;

/**
 * Use case for retrieving a single user by ID.
 */
class GetUserUseCase
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
     * @return User User entity
     * @throws NotFoundException When user not found
     */
    public function execute(int $user_id): User
    {
        $user = $this->user_repository->find_by_id($user_id);
        if ($user === null) {
            throw new NotFoundException("Usuário não encontrado");
        }

        return $user;
    }
}
