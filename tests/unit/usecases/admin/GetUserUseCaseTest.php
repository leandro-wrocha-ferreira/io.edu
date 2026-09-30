<?php

namespace tests\unit\usecases\admin;

use app\domain\exceptions\NotFoundException;
use app\domain\identity\value_objects\Email;
use app\domain\identity\User;
use app\usecases\admin\GetUserUseCase;
use tests\unit\mocks\repositories\MockUserRepository;

/**
 * Test suite for GetUserUseCase.
 */
class GetUserUseCaseTest extends \PHPUnit\Framework\TestCase
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
	 * Test retrieving a user by ID successfully.
	 *
	 * @return void
	 */
	public function test_get_user_success()
	{
		$user = User::create('Alice', new Email('alice@example.com'), 'password123');
		$user = $this->mock_user_repository->save($user);

		$use_case = new GetUserUseCase($this->mock_user_repository);
		$result = $use_case->execute($user->get_id());

		$this->assertEquals($user->get_id(), $result->get_id());
		$this->assertEquals('Alice', $result->get_name());
	}

	/**
	 * Test retrieving a non-existing user throws NotFoundException.
	 *
	 * @return void
	 */
	public function test_get_user_not_found_throws_exception()
	{
		$use_case = new GetUserUseCase($this->mock_user_repository);

		$this->expectException(NotFoundException::class);
		$this->expectExceptionMessage('User not found');

		$use_case->execute(999);
	}
}
