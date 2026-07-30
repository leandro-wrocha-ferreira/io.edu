<?php

namespace app\usecases\admin;

use app\domain\admin\role\Role;
use app\domain\admin\role\RoleRepositoryInterface;
use app\domain\exceptions\NotFoundException;
use app\factories\Model_factory;

/**
 * Use case for deleting a role.
 */
class DeleteRoleUseCase
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
     * @param int $role_id Role ID
     * @return void
     * @throws NotFoundException When role not found
     */
    public function execute(int $role_id): void
    {
        $role = $this->role_repository->find_by_id($role_id);
        if ($role === null) {
            throw new NotFoundException("Perfil não encontrado");
        }

        $this->role_repository->delete($role_id);
    }
}
