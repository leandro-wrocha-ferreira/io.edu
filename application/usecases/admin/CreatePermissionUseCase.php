<?php

namespace app\usecases\admin;

use app\domain\admin\Permission;
use app\factories\Model_factory;

/**
 * Use case for creating a new permission.
 */
class CreatePermissionUseCase
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
     * @param string $name Display name
     * @param string $slug Unique slug
     * @param string|null $description Optional description
     * @return Permission Created permission entity
     */
    public function execute(string $name, string $slug, ?string $description = null): Permission
    {
        $permission = Permission::create($name, $slug, $description);

        $this->permission_repository->save($permission);

        return $permission;
    }
}
