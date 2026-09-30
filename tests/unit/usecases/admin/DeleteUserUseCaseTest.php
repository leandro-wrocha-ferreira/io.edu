<?php

namespace tests\unit\usecases\admin;

use app\domain\exceptions\NotFoundException;
use app\domain\identity\value_objects\Email;
use app\domain\identity\User;
use app\usecases\admin\DeleteUserUseCase;
use tests\unit\mocks\repositories\MockUserRepository;

/**
 * Test suite for DeleteUserUseCase.
 */
class DeleteUserUseCaseTest extends \PHPUnit\Framework\TestCase
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
	 * Test soft-deleting a user successfully.
	 *
	 * @return void
	 */
	public function test_delete_user_success()
	{
		$user = User::create('Mark', new Email('mark@example.com'), 'password123');
		$user = $this->mock_user_repository->save($user);

		$use_case = new DeleteUserUseCase($this->mock_user_repository);
		$use_case->execute($user->get_id());

		$deleted_user = $this->mock_user_repository->find_by_id($user->get_id());
		$this->assertNotNull($deleted_user);
		$this->assertTrue($deleted_user->is_deleted());
	}

	/**
	 * Test deleting a non-existing user throws NotFoundException.
	 *
	 * @return void
	 */
	public function test_delete_user_not_found_throws_exception()
	{
		$use_case = new DeleteUserUseCase($this->mock_user_repository);

		$this->expectException(NotFoundException::class);
		$this->expectExceptionMessage('Usuário não encontrado');

		$use_case->execute(999);
	}
}
