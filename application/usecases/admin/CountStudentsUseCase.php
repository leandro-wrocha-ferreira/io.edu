<?php

namespace app\usecases\admin;

use app\factories\Model_factory;

/**
 * Use case for counting all active students in the system.
 */
class CountStudentsUseCase
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
     * @return int Total number of active students
     */
    public function execute(): int
    {
        return $this->user_repository->count_by_role('student');
    }
}
