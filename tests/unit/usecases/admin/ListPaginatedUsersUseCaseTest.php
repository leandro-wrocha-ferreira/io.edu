<?php

namespace tests\unit\usecases\admin;

use app\domain\identity\User;
use app\domain\identity\value_objects\Email;
use app\usecases\admin\ListPaginatedUsersUseCase;
use tests\unit\mocks\repositories\MockUserRepository;

/**
 * Test suite for ListPaginatedUsersUseCase.
 */
class ListPaginatedUsersUseCaseTest extends \PHPUnit\Framework\TestCase
{
	/** @var MockUserRepository */
	private $mock_user_repository;

	/**
	 * Set up test environment.
	 *
	 * @return void
	 */
	protected function setUp(): void
	{
		$this->mock_user_repository = new MockUserRepository();
	}

	/**
	 * Test listing paginated users with search and pagination.
	 *
	 * @return void
	 */
	public function test_list_paginated_users_success(): void
	{
		$user_one = User::create('Carlos Silva', new Email('carlos@example.com'), 'password123');
		$user_two = User::create('Beatriz Costa', new Email('beatriz@example.com'), 'password123');
		$this->mock_user_repository->save($user_one);
		$this->mock_user_repository->save($user_two);

		$use_case = new ListPaginatedUsersUseCase($this->mock_user_repository);
		$result = $use_case->execute(0, 10, 'Carlos', 'name', 'ASC');

		$this->assertArrayHasKey('data', $result);
		$this->assertArrayHasKey('recordsFiltered', $result);
		$this->assertArrayHasKey('recordsTotal', $result);
		$this->assertEquals(2, $result['recordsTotal']);
		$this->assertEquals(1, $result['recordsFiltered']);
		$this->assertCount(1, $result['data']);
		$this->assertEquals('Carlos Silva', $result['data'][0]->get_name());
	}
}
