<?php

namespace tests\unit\usecases\admin;

use app\domain\authorization\Permission;
use app\usecases\admin\ListPermissionsUseCase;
use tests\unit\mocks\repositories\MockPermissionRepository;

/**
 * Test suite for ListPermissionsUseCase.
 */
class ListPermissionsUseCaseTest extends \PHPUnit\Framework\TestCase
{
	/** @var MockPermissionRepository */
	private $mock_permission_repository;

	/**
	 * Set up test environment.
	 *
	 * @return void
	 */
	protected function setUp(): void
	{
		$this->mock_permission_repository = new MockPermissionRepository();
	}

	/**
	 * Test listing all permissions.
	 *
	 * @return void
	 */
	public function test_list_permissions_success()
	{
		$perm_view = Permission::create('View Users', 'users.view', 'Can view users');
		$perm_edit = Permission::create('Edit Users', 'users.edit', 'Can edit users');
		$this->mock_permission_repository->save($perm_view);
		$this->mock_permission_repository->save($perm_edit);

		$use_case = new ListPermissionsUseCase($this->mock_permission_repository);
		$result = $use_case->execute();

		$this->assertCount(2, $result);
		$this->assertEquals('View Users', $result[0]->get_name());
		$this->assertEquals('Edit Users', $result[1]->get_name());
	}
}
