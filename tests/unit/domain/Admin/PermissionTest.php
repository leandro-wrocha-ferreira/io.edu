<?php

use app\domain\admin\permission\Permission;

/**
 * Test suite for Permission domain entity.
 */
class PermissionTest extends \PHPUnit\Framework\TestCase
{
	public function test_create_permission(): void
	{
		$perm = Permission::create('Gerenciar Usuários', 'users.manage', 'Permite gerenciar usuários');

		$this->assertEquals('Gerenciar Usuários', $perm->get_name());
		$this->assertEquals('users.manage', $perm->get_slug());
		$this->assertEquals('Permite gerenciar usuários', $perm->get_description());
		$this->assertNull($perm->get_id());
	}

	public function test_permission_from_database(): void
	{
		$row = [
			'id' => 10,
			'name' => 'Visualizar Relatórios',
			'slug' => 'reports.view',
			'description' => 'Acesso aos relatórios',
		];

		$perm = Permission::from_database($row);

		$this->assertEquals(10, $perm->get_id());
		$this->assertEquals('Visualizar Relatórios', $perm->get_name());
		$this->assertEquals('reports.view', $perm->get_slug());
		$this->assertEquals('Acesso aos relatórios', $perm->get_description());
	}

	public function test_setters(): void
	{
		$perm = Permission::create('Teste', 'teste');
		$perm->set_name('Novo Nome');
		$perm->set_slug('novo.slug');
		$perm->set_description('Nova Descrição');

		$this->assertEquals('Novo Nome', $perm->get_name());
		$this->assertEquals('novo.slug', $perm->get_slug());
		$this->assertEquals('Nova Descrição', $perm->get_description());
	}
}
