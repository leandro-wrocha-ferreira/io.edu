<?php

namespace tests\unit\usecases\admin;

use app\domain\identity\Role;
use app\domain\exceptions\ConflictException;
use app\domain\exceptions\NotFoundException;
use app\usecases\admin\DeleteRoleUseCase;
use tests\unit\mocks\repositories\MockRoleRepository;

/**
 * Test suite for DeleteRoleUseCase.
 */
class DeleteRoleUseCaseTest extends \PHPUnit\Framework\TestCase
{
	/** @var MockRoleRepository */
	private $mock_role_repository;

	/**
	 * Set up test environment.
	 *
	 * @return void
	 */
	protected function setUp(): void
	{
		$this->mock_role_repository = new MockRoleRepository();
	}

	/**
	 * Test deleting a regular role successfully.
	 *
	 * @return void
	 */
	public function test_delete_role_success()
	{
		$role = Role::create('Tutor', 'tutor');
		$role = $this->mock_role_repository->save($role);

		$use_case = new DeleteRoleUseCase($this->mock_role_repository);
		$use_case->execute($role->get_id());

		$this->assertNull($this->mock_role_repository->find_by_id($role->get_id()));
	}

	/**
	 * Test deleting non-existing role throws NotFoundException.
	 *
	 * @return void
	 */
	public function test_delete_role_not_found_throws_exception()
	{
		$use_case = new DeleteRoleUseCase($this->mock_role_repository);

		$this->expectException(NotFoundException::class);
		$this->expectExceptionMessage('Perfil não encontrado');

		$use_case->execute(999);
	}

	/**
	 * Test deleting AdminMaster role throws ConflictException.
	 *
	 * @return void
	 */
	public function test_delete_admin_master_throws_conflict_exception()
	{
		$admin_master = Role::create('Admin Master', 'admin-master');
		$admin_master = $this->mock_role_repository->save($admin_master);

		$use_case = new DeleteRoleUseCase($this->mock_role_repository);

		$this->expectException(ConflictException::class);
		$this->expectExceptionMessage('Perfis padrão do sistema não podem ser excluídos.');

		$use_case->execute($admin_master->get_id());
	}
}
