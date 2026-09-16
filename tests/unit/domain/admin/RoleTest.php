<?php

use app\domain\admin\role\Role;

/**
 * Test suite for Role domain entity.
 */
class RoleTest extends \PHPUnit\Framework\TestCase
{
	public function test_create_role(): void
	{
		$role = Role::create('Professor', 'instructor', 'Acesso de instrutor');

		$this->assertEquals('Professor', $role->get_name());
		$this->assertEquals('instructor', $role->get_slug());
		$this->assertEquals('Acesso de instrutor', $role->get_description());
		$this->assertNull($role->get_id());
		$this->assertNotNull($role->get_created_at());
	}

	public function test_role_from_database(): void
	{
		$row = [
			'id' => 2,
			'name' => 'Administrador',
			'slug' => 'admin',
			'description' => 'Acesso total',
			'permission_ids' => '1,2,3',
			'created_at' => '2026-01-01 10:00:00',
		];

		$role = Role::from_database($row);

		$this->assertEquals(2, $role->get_id());
		$this->assertEquals('Administrador', $role->get_name());
		$this->assertEquals('admin', $role->get_slug());
		$this->assertEquals('Acesso total', $role->get_description());
		$this->assertEquals(['1', '2', '3'], $role->get_permission_ids());
		$this->assertEquals('2026-01-01 10:00:00', $role->get_created_at()->format('Y-m-d H:i:s'));
	}

	public function test_role_setters(): void
	{
		$role = Role::create('Teste', 'teste');
		$role->set_name('Nome Editado');
		$role->set_slug('slug.editado');
		$role->set_description('Desc Editada');
		$role->set_permission_ids([10, 20]);

		$this->assertEquals('Nome Editado', $role->get_name());
		$this->assertEquals('slug.editado', $role->get_slug());
		$this->assertEquals('Desc Editada', $role->get_description());
		$this->assertEquals([10, 20], $role->get_permission_ids());
	}
}
