<?php

namespace app\usecases\admin;

use app\domain\exceptions\NotFoundException;
use app\domain\exceptions\ValidationException;
use app\domain\identity\constants\RoleSlug;
use app\domain\identity\repositories\RoleRepositoryInterface;
use app\domain\identity\repositories\UserRepositoryInterface;
use app\domain\identity\User;
use app\domain\identity\value_objects\Email;

/**
 * Use case for updating an existing user.
 *
 * Does not handle password changes — use a separate reset flow.
 */
class UpdateUserUseCase
{
	/** @var UserRepositoryInterface */
	private UserRepositoryInterface $user_repository;

	/** @var RoleRepositoryInterface|null */
	private ?RoleRepositoryInterface $role_repository;

	/**
	 * Constructor.
	 *
	 * @param UserRepositoryInterface $user_repository
	 * @param RoleRepositoryInterface|null $role_repository
	 */
	public function __construct(
		UserRepositoryInterface $user_repository,
		?RoleRepositoryInterface $role_repository = null
	)
	{
		$this->user_repository = $user_repository;
		$this->role_repository = $role_repository;
	}

	/**
	 * Execute the use case.
	 *
	 * @param int $user_id User ID
	 * @param string $name New name
	 * @param string $email New email
	 * @param array<int> $role_ids Role IDs to assign
	 * @param bool $is_admin Whether the executing user has admin privileges
	 * @return User Updated user entity
	 * @throws NotFoundException|ValidationException When user not found or email already in use
	 */
	public function execute(
		int $user_id,
		string $name,
		string $email,
		array $role_ids = [],
		bool $is_admin = false
	): User
	{
		$user = $this->user_repository->find_by_id($user_id);
		if ($user === null) {
			throw new NotFoundException("Usuário não encontrado");
		}

		$email_vo = new Email($email);

		$existing = $this->user_repository->find_by_email($email_vo);
		if ($existing !== null && $existing->get_id() !== $user_id) {
			throw new ValidationException("E-mail já está em uso");
		}

		if (!$is_admin && !empty($role_ids) && $this->role_repository !== null) {
			$admin_role = $this->role_repository->find_by_slug(RoleSlug::ADMIN);
			if ($admin_role !== null) {
				$admin_role_id = $admin_role->get_id();
				$role_ids = array_values(array_filter($role_ids, function (int $role_id) use ($admin_role_id) {
					return $role_id !== $admin_role_id;
				}));
			}
		}

		$user->set_name($name);
		$user->set_email($email_vo);

		$saved_user = $this->user_repository->save($user);

		$this->user_repository->sync_user_roles($user_id, $role_ids);

		return $saved_user;
	}
}
