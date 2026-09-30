<?php

namespace app\usecases\identity;

use app\domain\identity\value_objects\Email;
use app\domain\identity\User;
use app\domain\identity\repositories\UserRepositoryInterface;
use app\domain\exceptions\UnauthorizedException;
use app\domain\exceptions\ForbiddenException;

/**
 * Use case for authenticating a user in the system.
 *
 * Validates credentials and checks for deactivated accounts.
 */
class AuthenticateUserUseCase
{
	/** @var UserRepositoryInterface */
	private UserRepositoryInterface $user_repository;

	/**
	 * Constructor.
	 *
	 * @param UserRepositoryInterface $user_repository
	 */
	public function __construct(UserRepositoryInterface $user_repository)
	{
		$this->user_repository = $user_repository;
	}

	/**
	 * Execute authentication.
	 *
	 * @param string $email User email
	 * @param string $password Plain text password
	 * @return User Authenticated user
	 * @throws UnauthorizedException When credentials are invalid
	 * @throws ForbiddenException When account is deactivated
	 */
	public function execute(string $email, string $password): User
	{
		$user = $this->user_repository->find_by_email(new Email($email));
		if ($user === null) {
			throw new UnauthorizedException("Invalid credentials");
		}

		if ($user->is_deleted()) {
			throw new ForbiddenException("Invalid credentials");
		}

		if (!$user->get_password()->verify($password)) {
			throw new UnauthorizedException("Invalid credentials");
		}

		return $user;
	}
}
