<?php

namespace app\usecases\admin;

use app\domain\admin\role\Role;
use app\domain\admin\role\RoleRepositoryInterface;
use app\factories\Model_factory;

/**
 * Use case for creating a new role.
 */
class CreateRoleUseCase
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
     * @param string $name Display name
     * @param string $slug Unique slug
     * @param string|null $description Optional description
     * @param array $permission_ids Permission IDs to assign
     * @return Role Created role entity
     */
    public function execute(string $name, string $slug, ?string $description = null, array $permission_ids = []): Role
    {
        $role = Role::create($name, $slug, $description);
        $role->set_permission_ids($permission_ids);

        $this->role_repository->save($role);

        return $role;
    }
}
