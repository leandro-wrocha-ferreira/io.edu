<?php

namespace app\usecases\admin;

use app\domain\exceptions\NotFoundException;
use app\domain\identity\repositories\UserRepositoryInterface;

/**
 * Use case for disabling a user.
 */
class DisableUserUseCase
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
	 * Disable the user.
	 *
	 * @param int $user_id User ID
	 * @return void
	 * @throws NotFoundException When user not found
	 */
	public function execute(int $user_id): void
	{
		$user = $this->user_repository->find_by_id($user_id);
		if ($user === null) {
			throw new NotFoundException("Usuário não encontrado");
		}

		$user->inactivate();
		$this->user_repository->save($user);
	}
}
