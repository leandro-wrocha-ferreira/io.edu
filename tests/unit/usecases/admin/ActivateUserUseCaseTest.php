<?php

namespace tests\unit\usecases\admin;

use app\domain\exceptions\NotFoundException;
use app\domain\identity\User;
use app\domain\identity\value_objects\Email;
use app\usecases\admin\ActivateUserUseCase;
use tests\unit\mocks\repositories\MockUserRepository;

/**
 * Test suite for ActivateUserUseCase.
 */
class ActivateUserUseCaseTest extends \PHPUnit\Framework\TestCase
{
	/** @var MockUserRepository */
	private $mock_user_repository;

	/**
	 * Set up test environment.
	 *
	 * Creates a fresh MockUserRepository before each test.
	 *
	 * @return void
	 */
	protected function setUp(): void
	{
		$this->mock_user_repository = new MockUserRepository();
	}

	/**
	 * Test successful user activation.
	 *
	 * @return void
	 */
	public function test_activate_user_success(): void
	{
		$email = new Email('inactive@example.com');
		$user = User::create('Inactive User', $email, 'password123', null, false);

		$saved_user = $this->mock_user_repository->save($user);
		$use_case = new ActivateUserUseCase($this->mock_user_repository);
		$result = $use_case->execute($saved_user->get_id());

		$this->assertTrue($result->is_active());
	}

	/**
	 * Test activation throws exception for non-existing user.
	 *
	 * @return void
	 */
	public function test_activate_user_not_found_throws_exception(): void
	{
		$use_case = new ActivateUserUseCase($this->mock_user_repository);

		$this->expectException(NotFoundException::class);
		$this->expectExceptionMessage('User not found');

		$use_case->execute(999);
	}
}
