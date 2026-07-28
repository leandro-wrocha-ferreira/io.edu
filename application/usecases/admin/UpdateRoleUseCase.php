<?php

namespace app\usecases\admin;

use app\domain\admin\Role;
use app\factories\Model_factory;

/**
 * Use case for updating an existing role.
 */
class UpdateRoleUseCase
{
    /** @var \app\domain\admin\RoleRepositoryInterface */
    private $role_repository;

    /**
     * Constructor.
     *
     * @param \app\domain\admin\RoleRepositoryInterface|null $repository Repository for testing (optional)
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
     * @param int $role_id Role ID
     * @param string $name Display name
     * @param string $slug Unique slug
     * @param string|null $description Optional description
     * @param array $permission_ids Permission IDs to assign
     * @return Role Updated role entity
     * @throws \RuntimeException When role not found
     */
    public function execute(int $role_id, string $name, string $slug, ?string $description = null, array $permission_ids = []): Role
    {
        $role = $this->role_repository->find_by_id($role_id);
        if ($role === null) {
            throw new \RuntimeException("Role not found");
        }

        $role->set_name($name);
        $role->set_slug($slug);
        $role->set_description($description);
        $role->set_permission_ids($permission_ids);

        $this->role_repository->save($role);

        return $role;
    }
}
