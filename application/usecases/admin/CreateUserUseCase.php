<?php

namespace app\usecases\admin;

use app\domain\exceptions\ValidationException;
use app\domain\identity\constants\RoleSlug;
use app\domain\identity\repositories\RoleRepositoryInterface;
use app\domain\identity\repositories\UserRepositoryInterface;
use app\domain\identity\User;
use app\domain\identity\value_objects\Email;

/**
 * Use case for creating a new user.
 */
class CreateUserUseCase
{
	/** @var UserRepositoryInterface */
	private UserRepositoryInterface $user_repository;

	/** @var RoleRepositoryInterface */
	private RoleRepositoryInterface $role_repository;

	/**
	 * Constructor.
	 *
	 * @param UserRepositoryInterface $user_repository
	 * @param RoleRepositoryInterface $role_repository
	 */
	public function __construct(
		UserRepositoryInterface $user_repository,
		RoleRepositoryInterface $role_repository
	)
	{
		$this->user_repository = $user_repository;
		$this->role_repository = $role_repository;
	}

	/**
	 * Execute the use case.
	 *
	 * @param string $name User's full name
	 * @param string $email User's email address
	 * @param string $password Plain text password
	 * @param array<int> $role_ids Role IDs to assign
	 * @param bool $is_admin Whether the actor performing creation is an admin
	 * @return User Created user entity
	 * @throws ValidationException When email already in use
	 */
	public function execute(
		string $name,
		string $email,
		string $password,
		array $role_ids = [],
		bool $is_admin = false
	): User
	{
		$email_vo = new Email($email);

		$existing = $this->user_repository->find_by_email($email_vo);
		if ($existing !== null) {
			throw new ValidationException("Email is already in use");
		}

		if (!$is_admin && !empty($role_ids)) {
			$admin_role = $this->role_repository->find_by_slug(RoleSlug::ADMIN);
			if ($admin_role !== null) {
				$admin_role_id = $admin_role->get_id();
				$role_ids = array_values(array_filter($role_ids, function (int $role_id) use ($admin_role_id) {
					return $role_id !== $admin_role_id;
				}));
			}
		}

		$user = User::create($name, $email_vo, $password);
		$saved_user = $this->user_repository->save($user);

		if (!empty($role_ids)) {
			$this->user_repository->sync_user_roles($saved_user->get_id(), $role_ids);
		}

		return $saved_user;
	}
}
