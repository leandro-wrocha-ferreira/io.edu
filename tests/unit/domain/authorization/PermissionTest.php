<?php

use app\domain\authorization\Permission;

/**
 * Test suite for Permission domain entity.
 */
class PermissionTest extends \PHPUnit\Framework\TestCase
{
	/**
	 * Test creating a permission without ID.
	 *
	 * @return void
	 */
	public function test_create_permission(): void
	{
		$permission = Permission::create('Gerenciar Usuários', 'users.manage', 'Permite gerenciar usuários');

		$this->assertEquals('Gerenciar Usuários', $permission->get_name());
		$this->assertEquals('users.manage', $permission->get_slug());
		$this->assertEquals('Permite gerenciar usuários', $permission->get_description());
		$this->assertNull($permission->get_id());
		$this->assertNotNull($permission->get_created_at());
	}

	/**
	 * Test creating a permission with ID.
	 *
	 * @return void
	 */
	public function test_create_permission_with_id(): void
	{
		$created_at = new \DateTime('2026-01-01 10:00:00');
		$permission = Permission::create('Visualizar Relatórios', 'reports.view', 'Acesso aos relatórios', 10, $created_at);

		$this->assertEquals(10, $permission->get_id());
		$this->assertEquals('Visualizar Relatórios', $permission->get_name());
		$this->assertEquals('reports.view', $permission->get_slug());
		$this->assertEquals('Acesso aos relatórios', $permission->get_description());
		$this->assertEquals('2026-01-01 10:00:00', $permission->get_created_at()->format('Y-m-d H:i:s'));
	}

	/**
	 * Test permission setters and getters.
	 *
	 * @return void
	 */
	public function test_permission_setters(): void
	{
		$permission = Permission::create('Teste', 'teste');
		$permission->set_name('Novo Nome');
		$permission->set_slug('novo.slug');
		$permission->set_description('Nova Descrição');

		$this->assertEquals('Novo Nome', $permission->get_name());
		$this->assertEquals('novo.slug', $permission->get_slug());
		$this->assertEquals('Nova Descrição', $permission->get_description());
	}
}
