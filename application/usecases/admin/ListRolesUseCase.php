<?php

namespace app\usecases\admin;

use app\domain\admin\role\Role;
use app\domain\admin\role\RoleRepositoryInterface;
use app\factories\Model_factory;

/**
 * Use case for listing all roles.
 */
class ListRolesUseCase
{
    /** @var RoleRepositoryInterface */
    private $role_repository;

    /**
     * Constructor.
     *
     * @param RoleRepositoryInterface|null $repository Repository for testing (optional)
     */
    public function __construct($repository = null)
    {
        if ($repository !== null) {
            $this->role_repository = $repository;
        } else {
            $this->role_repository = Model_factory::make('role_model');
        }
    }

    /**
     * Execute the use case.
     *
     * @return array List of Role entities
     */
    public function execute(): array
    {
        return $this->role_repository->find_all();
    }
}
