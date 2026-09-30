<?php

namespace tests\unit\usecases\admin;

use app\domain\identity\Role;
use app\domain\exceptions\ConflictException;
use app\domain\exceptions\NotFoundException;
use app\usecases\admin\UpdateRoleUseCase;
use tests\unit\mocks\repositories\MockPermissionRepository;
use tests\unit\mocks\repositories\MockRoleRepository;

/**
 * Test suite for UpdateRoleUseCase.
 */
class UpdateRoleUseCaseTest extends \PHPUnit\Framework\TestCase
{
	/** @var MockRoleRepository */
	private $mock_role_repository;

	/** @var MockPermissionRepository */
	private $mock_permission_repository;

	/**
	 * Set up test environment.
	 *
	 * @return void
	 */
	protected function setUp(): void
	{
		$this->mock_role_repository = new MockRoleRepository();
		$this->mock_permission_repository = new MockPermissionRepository();
	}

	/**
	 * Test updating a role successfully.
	 *
	 * @return void
	 */
	public function test_update_role_success(): void
	{
		$role = Role::create('Old Name', 'old-slug', 'Old description');
		$role = $this->mock_role_repository->save($role);

		$use_case = new UpdateRoleUseCase($this->mock_role_repository, $this->mock_permission_repository);
		$updated_role = $use_case->execute($role->get_id(), 'New Name', 'new-slug', 'New description', [2, 3]);

		$this->assertEquals('New Name', $updated_role->get_name());
		$this->assertEquals('new-slug', $updated_role->get_slug());
		$this->assertEquals('New description', $updated_role->get_description());
		$this->assertEquals([2, 3], $this->mock_permission_repository->find_ids_by_role_id($role->get_id()));
	}

	/**
	 * Test updating non-existing role throws NotFoundException.
	 *
	 * @return void
	 */
	public function test_update_role_not_found_throws_exception()
	{
		$use_case = new UpdateRoleUseCase($this->mock_role_repository, $this->mock_permission_repository);

		$this->expectException(NotFoundException::class);
		$this->expectExceptionMessage('Role not found');

		$use_case->execute(999, 'Name', 'slug');
	}

	/**
	 * Test updating AdminMaster role throws ConflictException.
	 *
	 * @return void
	 */
	public function test_update_admin_master_throws_conflict_exception()
	{
		$admin_master = Role::create('Admin Master', 'admin-master');
		$admin_master = $this->mock_role_repository->save($admin_master);

		$use_case = new UpdateRoleUseCase($this->mock_role_repository, $this->mock_permission_repository);

		$this->expectException(ConflictException::class);
		$this->expectExceptionMessage('The AdminMaster role is protected and cannot be modified');

		$use_case->execute($admin_master->get_id(), 'New Master', 'new-master');
	}
}
