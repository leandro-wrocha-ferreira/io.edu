<?php

namespace tests\unit\usecases\admin;

use app\domain\identity\Role;
use app\usecases\admin\ListPaginatedRolesUseCase;
use tests\unit\mocks\repositories\MockRoleRepository;

/**
 * Test suite for ListPaginatedRolesUseCase.
 */
class ListPaginatedRolesUseCaseTest extends \PHPUnit\Framework\TestCase
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
	 * Test listing paginated roles with search and ordering.
	 *
	 * @return void
	 */
	public function test_list_paginated_roles_success(): void
	{
		$role_alpha = Role::create('Alpha Role', 'alpha');
		$role_beta = Role::create('Beta Role', 'beta');
		$this->mock_role_repository->save($role_alpha);
		$this->mock_role_repository->save($role_beta);

		$use_case = new ListPaginatedRolesUseCase($this->mock_role_repository);
		$result = $use_case->execute(0, 10, 'Alpha', 'name', 'ASC');

		$this->assertArrayHasKey('data', $result);
		$this->assertArrayHasKey('recordsFiltered', $result);
		$this->assertArrayHasKey('recordsTotal', $result);
		$this->assertEquals(2, $result['recordsTotal']);
		$this->assertEquals(1, $result['recordsFiltered']);
		$this->assertCount(1, $result['data']);
		$this->assertEquals('Alpha Role', $result['data'][0]->get_name());
	}
}
