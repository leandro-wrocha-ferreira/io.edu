<?php

namespace tests\unit\usecases\admin;

use app\domain\exceptions\NotFoundException;
use app\domain\exceptions\ValidationException;
use app\domain\identity\Role;
use app\domain\identity\User;
use app\domain\identity\value_objects\Email;
use app\usecases\admin\UpdateUserUseCase;
use tests\unit\mocks\repositories\MockRoleRepository;
use tests\unit\mocks\repositories\MockUserRepository;

/**
 * Test suite for UpdateUserUseCase.
 */
class UpdateUserUseCaseTest extends \PHPUnit\Framework\TestCase
{
	/** @var MockUserRepository */
	private MockUserRepository $mock_user_repository;

	/** @var MockRoleRepository */
	private MockRoleRepository $mock_role_repository;

	/**
	 * Set up test environment.
	 *
	 * @return void
	 */
	protected function setUp(): void
	{
		$this->mock_user_repository = new MockUserRepository();
		$this->mock_role_repository = new MockRoleRepository();
	}

	/**
	 * Test updating a user successfully.
	 *
	 * @return void
	 */
	public function test_update_user_success(): void
	{
		$user = User::create('Old Name', new Email('old@example.com'), 'password123');
		$user = $this->mock_user_repository->save($user);

		$use_case = new UpdateUserUseCase($this->mock_user_repository);
		$updated_user = $use_case->execute($user->get_id(), 'Updated Name', 'updated@example.com', [2]);

		$this->assertEquals('Updated Name', $updated_user->get_name());
		$this->assertEquals('updated@example.com', (string) $updated_user->get_email());
		$this->assertEquals([2], $this->mock_user_repository->find_role_ids_by_user_id($user->get_id()));
	}

	/**
	 * Test updating user clears assigned roles when an empty array is provided.
	 *
	 * @return void
	 */
	public function test_update_user_clears_roles_when_empty_array_provided(): void
	{
		$user = User::create('Has Roles', new Email('roles@example.com'), 'password123');
		$user = $this->mock_user_repository->save($user);
		$this->mock_user_repository->sync_user_roles($user->get_id(), [2, 3]);

		$use_case = new UpdateUserUseCase($this->mock_user_repository);
		$use_case->execute($user->get_id(), 'No Roles Now', 'roles@example.com', []);

		$this->assertEmpty($this->mock_user_repository->find_role_ids_by_user_id($user->get_id()));
	}

	/**
	 * Test filtering admin role when updater is not admin.
	 *
	 * @return void
	 */
	public function test_update_user_filters_admin_role_when_not_admin(): void
	{
		$admin_role = Role::create('Administrador', 'admin');
		$admin_role = $this->mock_role_repository->save($admin_role);

		$student_role = Role::create('Aluno', 'student');
		$student_role = $this->mock_role_repository->save($student_role);

		$user = User::create('User To Update', new Email('target@example.com'), 'password123');
		$user = $this->mock_user_repository->save($user);

		$use_case = new UpdateUserUseCase($this->mock_user_repository, $this->mock_role_repository);
		$use_case->execute(
			$user->get_id(),
			'Target Updated',
			'target@example.com',
			[$admin_role->get_id(), $student_role->get_id()],
			false
		);

		$assigned_ids = $this->mock_user_repository->find_role_ids_by_user_id($user->get_id());
		$this->assertNotContains($admin_role->get_id(), $assigned_ids);
		$this->assertContains($student_role->get_id(), $assigned_ids);
	}

	/**
	 * Test updating a non-existing user throws NotFoundException.
	 *
	 * @return void
	 */
	public function test_update_user_not_found_throws_exception()
	{
		$use_case = new UpdateUserUseCase($this->mock_user_repository);

		$this->expectException(NotFoundException::class);
		$this->expectExceptionMessage('Usuário não encontrado');

		$use_case->execute(999, 'Name', 'valid@example.com');
	}

	/**
	 * Test updating email to an already taken email throws ValidationException.
	 *
	 * @return void
	 */
	public function test_update_user_email_already_in_use_throws_validation_exception()
	{
		$user_one = User::create('User One', new Email('user1@example.com'), 'password123');
		$user_two = User::create('User Two', new Email('user2@example.com'), 'password123');
		$this->mock_user_repository->save($user_one);
		$user_two = $this->mock_user_repository->save($user_two);

		$use_case = new UpdateUserUseCase($this->mock_user_repository);

		$this->expectException(ValidationException::class);
		$this->expectExceptionMessage('E-mail já está em uso');

		$use_case->execute($user_two->get_id(), 'User Two Updated', 'user1@example.com');
	}
}
