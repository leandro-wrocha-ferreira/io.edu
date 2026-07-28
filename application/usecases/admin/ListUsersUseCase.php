<?php

namespace app\usecases\admin;

use app\domain\identity\User;
use app\factories\Model_factory;

/**
 * Use case for listing all non-deleted users.
 */
class ListUsersUseCase
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
     * @return array List of User entities
     */
    public function execute(): array
    {
        return $this->user_repository->find_all();
    }
}
