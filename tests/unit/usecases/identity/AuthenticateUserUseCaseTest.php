<?php

namespace tests\unit\usecases\identity;

use app\domain\exceptions\ForbiddenException;
use app\domain\exceptions\UnauthorizedException;
use app\domain\identity\Email;
use app\domain\identity\User;
use app\usecases\identity\AuthenticateUserUseCase;
use tests\unit\mocks\repositories\MockUserRepository;

/**
 * Test suite for AuthenticateUserUseCase.
 *
 * Covers successful authentication, invalid email, invalid password,
 * and deactivated account scenarios.
 */
class AuthenticateUserUseCaseTest extends \PHPUnit\Framework\TestCase
{
	/** @var MockUserRepository */
	private $mock_repository;

	/**
	 * Set up test environment.
	 *
	 * Creates a fresh MockUserRepository before each test.
	 *
	 * @return void
	 */
	protected function setUp(): void
	{
		$this->mock_repository = new MockUserRepository();
	}

	/**
	 * Test successful authentication with valid credentials.
	 *
	 * @return void
	 */
	public function test_authenticate_with_valid_credentials()
	{
		$email = new Email('john@example.com');
		$user = User::create('John', $email, 'password123');

		$this->mock_repository->save($user);

		$use_case = new AuthenticateUserUseCase($this->mock_repository);
		$result = $use_case->execute('john@example.com', 'password123');

		$this->assertEquals('John', $result->get_name());
	}

	/**
	 * Test that authentication throws exception for non-existing email.
	 *
	 * @return void
	 */
	public function test_authenticate_with_invalid_email_throws_exception()
	{
		$use_case = new AuthenticateUserUseCase($this->mock_repository);

		$this->expectException(UnauthorizedException::class);
		$this->expectExceptionMessage('Invalid credentials');
		$use_case->execute('notfound@example.com', 'password123');
	}

	/**
	 * Test that authentication throws exception for wrong password.
	 *
	 * @return void
	 */
	public function test_authenticate_with_invalid_password_throws_exception()
	{
		$email = new Email('john@example.com');
		$user = User::create('John', $email, 'correctpassword');

		$this->mock_repository->save($user);

		$use_case = new AuthenticateUserUseCase($this->mock_repository);

		$this->expectException(UnauthorizedException::class);
		$this->expectExceptionMessage('Invalid credentials');
		$use_case->execute('john@example.com', 'wrongpassword');
	}

	/**
	 * Test that authentication throws exception for deactivated account.
	 *
	 * @return void
	 */
	public function test_authenticate_with_deactivated_account_throws_exception()
	{
		$email = new Email('deleted@example.com');
		$user = User::create('Deleted User', $email, 'password123');
		
		$this->mock_repository->save($user);
		$this->mock_repository->delete($user);

		$use_case = new AuthenticateUserUseCase($this->mock_repository);

		$this->expectException(ForbiddenException::class);
		$this->expectExceptionMessage('Invalid credentials');
		$use_case->execute('deleted@example.com', 'password123');
	}
}
