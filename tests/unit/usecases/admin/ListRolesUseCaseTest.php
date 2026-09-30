<?php

namespace tests\unit\usecases\admin;

use app\domain\identity\Role;
use app\usecases\admin\ListRolesUseCase;
use tests\unit\mocks\repositories\MockRoleRepository;

/**
 * Test suite for ListRolesUseCase.
 */
class ListRolesUseCaseTest extends \PHPUnit\Framework\TestCase
{
	/** @var MockRoleRepository */
	private $mock_role_repository;

	/**
	 * Set up test environment.
	 *
	 * @return void
	 */
	protected function setUp(): void
	{
		$this->mock_role_repository = new MockRoleRepository();
	}

	/**
	 * Test listing all roles.
	 *
	 * @return void
	 */
	public function test_list_roles_success()
	{
		$role_student = Role::create('Student', 'student');
		$role_admin = Role::create('Admin', 'admin');
		$this->mock_role_repository->save($role_student);
		$this->mock_role_repository->save($role_admin);

		$use_case = new ListRolesUseCase($this->mock_role_repository);
		$result = $use_case->execute();

		$this->assertCount(2, $result);
		$this->assertEquals('Student', $result[0]->get_name());
		$this->assertEquals('Admin', $result[1]->get_name());
	}
}
