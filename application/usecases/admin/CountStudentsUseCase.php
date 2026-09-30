<?php

namespace app\usecases\admin;

use app\domain\identity\repositories\UserRepositoryInterface;

/**
 * Use case for counting all active students in the system.
 */
class CountStudentsUseCase
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
	 * @return int Total number of active students
	 */
	public function execute(): int
	{
		return $this->user_repository->count_by_role('student');
	}
}
