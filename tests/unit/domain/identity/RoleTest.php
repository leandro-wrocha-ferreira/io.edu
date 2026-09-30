<?php

use app\domain\identity\Role;

/**
 * Test suite for Role domain entity.
 */
class RoleTest extends \PHPUnit\Framework\TestCase
{
	/**
	 * Test creating a role without ID.
	 *
	 * @return void
	 */
	public function test_create_role(): void
	{
		$role = Role::create('Professor', 'instructor', 'Acesso de instrutor');

		$this->assertEquals('Professor', $role->get_name());
		$this->assertEquals('instructor', $role->get_slug());
		$this->assertEquals('Acesso de instrutor', $role->get_description());
		$this->assertNull($role->get_id());
		$this->assertNotNull($role->get_created_at());
	}

	/**
	 * Test creating a role with ID.
	 *
	 * @return void
	 */
	public function test_create_role_with_id(): void
	{
		$created_at = new \DateTime('2026-01-01 10:00:00');
		$role = Role::create('Administrador', 'admin', 'Acesso total', 2, $created_at);

		$this->assertEquals(2, $role->get_id());
		$this->assertEquals('Administrador', $role->get_name());
		$this->assertEquals('admin', $role->get_slug());
		$this->assertEquals('Acesso total', $role->get_description());
		$this->assertEquals('2026-01-01 10:00:00', $role->get_created_at()->format('Y-m-d H:i:s'));
	}

	/**
	 * Test role setters and getters.
	 *
	 * @return void
	 */
	public function test_role_setters(): void
	{
		$role = Role::create('Teste', 'teste');
		$role->set_name('Nome Editado');
		$role->set_slug('slug-editado');
		$role->set_description('Desc Editada');

		$this->assertEquals('Nome Editado', $role->get_name());
		$this->assertEquals('slug-editado', $role->get_slug());
		$this->assertEquals('Desc Editada', $role->get_description());
	}
}
