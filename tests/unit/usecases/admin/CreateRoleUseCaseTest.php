<?php

namespace tests\unit\usecases\admin;

use app\domain\identity\Role;
use app\usecases\admin\CreateRoleUseCase;
use tests\unit\mocks\repositories\MockPermissionRepository;
use tests\unit\mocks\repositories\MockRoleRepository;

/**
 * Test suite for CreateRoleUseCase.
 */
class CreateRoleUseCaseTest extends \PHPUnit\Framework\TestCase
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
	 * Test creating a role successfully.
	 *
	 * @return void
	 */
	public function test_create_role_success(): void
	{
		$use_case = new CreateRoleUseCase($this->mock_role_repository, $this->mock_permission_repository);
		$created_role = $use_case->execute('Instructor', 'instructor', 'Teacher role', [1, 2]);

		$this->assertInstanceOf(Role::class, $created_role);
		$this->assertEquals('Instructor', $created_role->get_name());
		$this->assertEquals('instructor', $created_role->get_slug());
		$this->assertEquals('Teacher role', $created_role->get_description());
		$this->assertCount(1, $this->mock_role_repository->find_all());
		$this->assertEquals([1, 2], $this->mock_permission_repository->find_ids_by_role_id($created_role->get_id()));
	}
}

