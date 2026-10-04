<?php

namespace app\usecases\admin;

use app\domain\exceptions\NotFoundException;
use app\domain\identity\User;
use app\domain\identity\repositories\UserRepositoryInterface;

/**
 * Use case for retrieving a single user by ID.
 */
class GetUserUseCase
{
	/** @var UserRepositoryInterface */
	private $user_repository;

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
	 * Execute the use case.
	 *
	 * @param int $user_id User ID
	 * @return User User entity
	 * @throws NotFoundException When user not found
	 */
	public function execute(int $user_id): User
	{
		$user = $this->user_repository->find_by_id($user_id);
		if ($user === null) {
			throw new NotFoundException("User not found");
		}

		return $user;
	}

	/**
	 * Get role IDs assigned to a user.
	 *
	 * @param int $user_id User ID
	 * @return array<int>
	 */
	public function get_role_ids(int $user_id): array
	{
		return $this->user_repository->find_role_ids_by_user_id($user_id);
	}
}
