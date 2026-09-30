<?php

namespace tests\unit\usecases\admin;

use app\domain\identity\Role;
use app\domain\exceptions\NotFoundException;
use app\usecases\admin\GetRoleUseCase;
use tests\unit\mocks\repositories\MockRoleRepository;

/**
 * Test suite for GetRoleUseCase.
 */
class GetRoleUseCaseTest extends \PHPUnit\Framework\TestCase
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
	 * Test getting a role by ID successfully.
	 *
	 * @return void
	 */
	public function test_get_role_success(): void
	{
		$role = Role::create('Manager', 'manager', 'Manager description');
		$saved_role = $this->mock_role_repository->save($role);

		$use_case = new GetRoleUseCase($this->mock_role_repository);
		$result = $use_case->execute($saved_role->get_id());

		$this->assertEquals($saved_role->get_id(), $result->get_id());
		$this->assertEquals('Manager', $result->get_name());
	}

	/**
	 * Test getting a non-existing role throws NotFoundException.
	 *
	 * @return void
	 */
	public function test_get_role_not_found_throws_exception(): void
	{
		$use_case = new GetRoleUseCase($this->mock_role_repository);

		$this->expectException(NotFoundException::class);
		$this->expectExceptionMessage('Role not found');

		$use_case->execute(999);
	}
}

