<?php

namespace tests\unit\usecases\admin;

use app\domain\exceptions\ValidationException;
use app\domain\identity\constants\RoleSlug;
use app\domain\identity\Role;
use app\domain\identity\User;
use app\domain\identity\value_objects\Email;
use app\usecases\admin\CreateUserUseCase;
use tests\unit\mocks\repositories\MockRoleRepository;
use tests\unit\mocks\repositories\MockUserRepository;

/**
 * Test suite for CreateUserUseCase.
 */
class CreateUserUseCaseTest extends \PHPUnit\Framework\TestCase
{
	/** @var MockUserRepository */
	private MockUserRepository $mock_user_repository;

	/** @var MockRoleRepository */
	private MockRoleRepository $mock_role_repository;

	/**
	 * Set up test environment.
	 *
	 * Creates fresh MockUserRepository and MockRoleRepository before each test.
	 *
	 * @return void
	 */
	protected function setUp(): void
	{
		$this->mock_user_repository = new MockUserRepository();
		$this->mock_role_repository = new MockRoleRepository();

		$this->mock_role_repository->save(Role::create('Admin', RoleSlug::ADMIN, 'Administrator', 1));
		$this->mock_role_repository->save(Role::create('Student', RoleSlug::STUDENT, 'Student', 2));
	}

	/**
	 * Test creating a user successfully by admin with multiple roles.
	 *
	 * @return void
	 */
	public function test_create_user_success_by_admin(): void
	{
		$use_case = new CreateUserUseCase($this->mock_user_repository, $this->mock_role_repository);
		$created_user = $use_case->execute('Jane Doe', 'jane@example.com', 'secret123', [1, 2], true);

		$this->assertInstanceOf(User::class, $created_user);
		$this->assertEquals('Jane Doe', $created_user->get_name());
		$this->assertEquals('jane@example.com', (string) $created_user->get_email());
		$this->assertEquals([1, 2], $this->mock_user_repository->find_role_ids_by_user_id($created_user->get_id()));
		$this->assertCount(1, $this->mock_user_repository->find_all());
	}

	/**
	 * Test that a non-admin actor cannot assign the Admin role.
	 *
	 * @return void
	 */
	public function test_create_user_non_admin_cannot_assign_admin_role(): void
	{
		$use_case = new CreateUserUseCase($this->mock_user_repository, $this->mock_role_repository);
		$created_user = $use_case->execute('Jane Doe', 'jane@example.com', 'secret123', [1, 2], false);

		$this->assertInstanceOf(User::class, $created_user);
		$this->assertEquals([2], $this->mock_user_repository->find_role_ids_by_user_id($created_user->get_id()));
	}

	/**
	 * Test creating a user without any roles.
	 *
	 * @return void
	 */
	public function test_create_user_without_roles(): void
	{
		$use_case = new CreateUserUseCase($this->mock_user_repository, $this->mock_role_repository);
		$created_user = $use_case->execute('Jane Doe', 'jane@example.com', 'secret123', [], false);

		$this->assertInstanceOf(User::class, $created_user);
		$this->assertEquals([], $this->mock_user_repository->find_role_ids_by_user_id($created_user->get_id()));
	}

	/**
	 * Test creating a user with duplicate email throws ValidationException.
	 *
	 * @return void
	 */
	public function test_create_user_duplicate_email_throws_validation_exception(): void
	{
		$existing_user = User::create('Existing', new Email('duplicate@example.com'), 'pass123');
		$this->mock_user_repository->save($existing_user);

		$use_case = new CreateUserUseCase($this->mock_user_repository, $this->mock_role_repository);

		$this->expectException(ValidationException::class);
		$this->expectExceptionMessage('Email is already in use');

		$use_case->execute('Another Name', 'duplicate@example.com', 'newpass123');
	}
}
