<?php

use app\domain\admin\permission\Permission;
use app\domain\admin\permission\PermissionRepositoryInterface;
use app\domain\admin\role\Role;
use app\domain\admin\role\RoleRepositoryInterface;
use app\domain\exceptions\NotFoundException;
use app\usecases\admin\CreatePermissionUseCase;
use app\usecases\admin\CreateRoleUseCase;
use app\usecases\admin\DeleteRoleUseCase;
use app\usecases\admin\GetRoleUseCase;
use app\usecases\admin\ListPaginatedRolesUseCase;
use app\usecases\admin\ListPermissionsUseCase;
use app\usecases\admin\ListRolesUseCase;
use app\usecases\admin\UpdateRoleUseCase;

class MockRoleRepository implements RoleRepositoryInterface
{
	public array $roles = [];

	public function find_by_id(int $id): ?Role
	{
		foreach ($this->roles as $r) {
			if ($r->get_id() === $id) {
				return $r;
			}
		}
		return null;
	}

	public function find_all(): array
	{
		return $this->roles;
	}

	public function save(Role $role): void
	{
		if ($role->get_id() === null) {
			$reflection = new ReflectionClass($role);
			$prop = $reflection->getProperty('id');
			$prop->setAccessible(true);
			$prop->setValue($role, count($this->roles) + 1);
			$this->roles[] = $role;
		} else {
			foreach ($this->roles as $k => $r) {
				if ($r->get_id() === $role->get_id()) {
					$this->roles[$k] = $role;
					return;
				}
			}
			$this->roles[] = $role;
		}
	}

	public function delete(int $id): void
	{
		$this->roles = array_filter($this->roles, fn($r) => $r->get_id() !== $id);
	}

	public function find_paginated(int $start, int $length, string $search, string $order_col, string $order_dir): array
	{
		$data = array_map(fn($r) => [
			'id' => $r->get_id(),
			'name' => $r->get_name(),
			'slug' => $r->get_slug(),
		], $this->roles);

		return [
			'data' => array_slice($data, $start, $length),
			'recordsFiltered' => count($this->roles),
		];
	}

	public function count_all(): int
	{
		return count($this->roles);
	}
}

class MockPermissionRepository implements PermissionRepositoryInterface
{
	public array $permissions = [];

	public function find_all(): array
	{
		return $this->permissions;
	}

	public function find_by_id(int $id): ?Permission
	{
		foreach ($this->permissions as $p) {
			if ($p->get_id() === $id) {
				return $p;
			}
		}
		return null;
	}

	public function save(Permission $permission): void
	{
		if ($permission->get_id() === null) {
			$reflection = new ReflectionClass($permission);
			$prop = $reflection->getProperty('id');
			$prop->setAccessible(true);
			$prop->setValue($permission, count($this->permissions) + 1);
			$this->permissions[] = $permission;
		} else {
			foreach ($this->permissions as $k => $p) {
				if ($p->get_id() === $permission->get_id()) {
					$this->permissions[$k] = $permission;
					return;
				}
			}
			$this->permissions[] = $permission;
		}
	}

	public function delete(int $id): void
	{
		$this->permissions = array_filter($this->permissions, fn($p) => $p->get_id() !== $id);
	}
}

/**
 * Test suite for Role and Permission Use Cases.
 */
class AdminRoleAndPermissionUseCasesTest extends \PHPUnit\Framework\TestCase
{
	private MockRoleRepository $roleRepo;
	private MockPermissionRepository $permRepo;

	protected function setUp(): void
	{
		$this->roleRepo = new MockRoleRepository();
		$this->permRepo = new MockPermissionRepository();
	}

	public function test_create_role(): void
	{
		$useCase = new CreateRoleUseCase($this->roleRepo);
		$role = $useCase->execute('Coordenador', 'coordinator', 'Gestão pedagógica', [1, 2]);

		$this->assertEquals('Coordenador', $role->get_name());
		$this->assertEquals('coordinator', $role->get_slug());
		$this->assertEquals([1, 2], $role->get_permission_ids());
		$this->assertCount(1, $this->roleRepo->roles);
	}

	public function test_update_role_success(): void
	{
		$role = Role::create('Tutor', 'tutor');
		$this->roleRepo->save($role);

		$useCase = new UpdateRoleUseCase($this->roleRepo);
		$updated = $useCase->execute($role->get_id(), 'Tutor Chefe', 'tutor-chefe', 'Novo', [5]);

		$this->assertEquals('Tutor Chefe', $updated->get_name());
		$this->assertEquals('tutor-chefe', $updated->get_slug());
		$this->assertEquals([5], $updated->get_permission_ids());
	}

	public function test_update_role_not_found(): void
	{
		$this->expectException(NotFoundException::class);
		$useCase = new UpdateRoleUseCase($this->roleRepo);
		$useCase->execute(999, 'Inexistente', 'inexistente');
	}

	public function test_get_and_delete_role(): void
	{
		$role = Role::create('Excluir', 'excluir');
		$this->roleRepo->save($role);

		$getUseCase = new GetRoleUseCase($this->roleRepo);
		$found = $getUseCase->execute($role->get_id());
		$this->assertEquals('Excluir', $found->get_name());

		$deleteUseCase = new DeleteRoleUseCase($this->roleRepo);
		$deleteUseCase->execute($role->get_id());
		$this->assertCount(0, $this->roleRepo->roles);
	}

	public function test_list_roles_and_pagination(): void
	{
		$r1 = Role::create('R1', 'r1');
		$r2 = Role::create('R2', 'r2');
		$this->roleRepo->save($r1);
		$this->roleRepo->save($r2);

		$listUseCase = new ListRolesUseCase($this->roleRepo);
		$all = $listUseCase->execute();
		$this->assertCount(2, $all);

		$paginatedUseCase = new ListPaginatedRolesUseCase($this->roleRepo);
		$res = $paginatedUseCase->execute(0, 10, '', 'name', 'ASC');
		$this->assertEquals(2, $res['recordsTotal']);
		$this->assertCount(2, $res['data']);
	}

	public function test_create_and_list_permissions(): void
	{
		$createUseCase = new CreatePermissionUseCase($this->permRepo);
		$perm = $createUseCase->execute('Ver Cursos', 'courses.view', 'Visualizar cursos');

		$this->assertEquals('Ver Cursos', $perm->get_name());
		$this->assertEquals('courses.view', $perm->get_slug());
		$this->assertCount(1, $this->permRepo->permissions);

		$listUseCase = new ListPermissionsUseCase($this->permRepo);
		$all = $listUseCase->execute();
		$this->assertCount(1, $all);
	}
}
