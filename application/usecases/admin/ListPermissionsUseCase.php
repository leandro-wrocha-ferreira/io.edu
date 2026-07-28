<?php

namespace app\usecases\admin;

use app\domain\admin\Permission;
use app\factories\Model_factory;

/**
 * Use case for listing all permissions.
 */
class ListPermissionsUseCase
{
    /** @var \app\domain\admin\PermissionRepositoryInterface */
    private $permission_repository;

    /**
     * Constructor.
     *
     * @param \app\domain\admin\PermissionRepositoryInterface|null $repository Repository for testing (optional)
     */
    public function __construct($repository = null)
    {
        if ($repository !== null) {
            $this->permission_repository = $repository;
        } else {
            $this->permission_repository = Model_factory::make('permission_model');
        }
    }

    /**
     * Execute the use case.
     *
     * @return array List of Permission entities
     */
    public function execute(): array
    {
        return $this->permission_repository->find_all();
    }
}
