<?php

namespace tests\unit\usecases\admin;

use app\domain\exceptions\NotFoundException;
use app\domain\identity\User;
use app\domain\identity\value_objects\Email;
use app\usecases\admin\DisableUserUseCase;
use tests\unit\mocks\repositories\MockUserRepository;

/**
 * Test suite for DisableUserUseCase.
 */
class DisableUserUseCaseTest extends \PHPUnit\Framework\TestCase
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
	 * Test disabling an active user.
	 *
	 * @return void
	 */
	public function test_disable_user_success()
	{
		$user = User::create('Active User', new Email('active@example.com'), 'password123');
		$user = $this->mock_user_repository->save($user);

		$use_case = new DisableUserUseCase($this->mock_user_repository);
		$use_case->execute($user->get_id());

		$disabled_user = $this->mock_user_repository->find_by_id($user->get_id());
		$this->assertFalse($disabled_user->is_active());
	}

	/**
	 * Test disabling a non-existing user throws NotFoundException.
	 *
	 * @return void
	 */
	public function test_disable_user_not_found_throws_exception()
	{
		$use_case = new DisableUserUseCase($this->mock_user_repository);

		$this->expectException(NotFoundException::class);
		$this->expectExceptionMessage('Usuário não encontrado');

		$use_case->execute(999);
	}
}
