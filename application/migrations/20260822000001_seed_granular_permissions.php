<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Migration to seed granular permissions and assign them to the Admin role (id = 2).
 */
class Migration_Seed_granular_permissions extends CI_Migration {

	public function up()
	{
		$permissions = array(
			array('name' => 'Visualizar Painel', 'slug' => 'dashboard.view', 'description' => 'Permite visualizar o painel inicial'),
			array('name' => 'Visualizar Usuários', 'slug' => 'users.view', 'description' => 'Permite visualizar a lista de usuários'),
			array('name' => 'Criar Usuário', 'slug' => 'users.create', 'description' => 'Permite cadastrar novos usuários'),
			array('name' => 'Editar Usuário', 'slug' => 'users.edit', 'description' => 'Permite editar dados de usuários'),
			array('name' => 'Alternar Status de Usuário', 'slug' => 'users.toggle_status', 'description' => 'Permite ativar ou desativar usuários'),
			array('name' => 'Excluir Usuário', 'slug' => 'users.delete', 'description' => 'Permite excluir usuários'),
			array('name' => 'Visualizar Perfis', 'slug' => 'roles.view', 'description' => 'Permite visualizar a lista de perfis de acesso'),
			array('name' => 'Criar Perfil', 'slug' => 'roles.create', 'description' => 'Permite cadastrar novos perfis'),
			array('name' => 'Editar Perfil', 'slug' => 'roles.edit', 'description' => 'Permite editar dados e permissões dos perfis'),
			array('name' => 'Excluir Perfil', 'slug' => 'roles.delete', 'description' => 'Permite excluir perfis de acesso'),
		);

		foreach ($permissions as $perm)
		{
			$existing = $this->db->get_where('permissions', array('slug' => $perm['slug']))->row();
			if ($existing)
			{
				$perm_id = $existing->id;
			}
			else
			{
				$this->db->insert('permissions', $perm);
				$perm_id = $this->db->insert_id();
			}

			// Link to Admin role (id = 2) if not already linked
			$role_perm = $this->db->get_where('role_permissions', array('role_id' => 2, 'permission_id' => $perm_id))->row();
			if ( ! $role_perm)
			{
				$this->db->insert('role_permissions', array(
					'role_id' => 2,
					'permission_id' => $perm_id,
				));
			}
		}
	}

	public function down()
	{
		$slugs = array(
			'dashboard.view',
			'users.view',
			'users.create',
			'users.edit',
			'users.toggle_status',
			'users.delete',
			'roles.view',
			'roles.create',
			'roles.edit',
			'roles.delete',
		);

		$this->db->where_in('slug', $slugs);
		$perms = $this->db->get('permissions')->result_array();

		if ( ! empty($perms))
		{
			$perm_ids = array_column($perms, 'id');
			$this->db->where_in('permission_id', $perm_ids)->delete('role_permissions');
			$this->db->where_in('id', $perm_ids)->delete('permissions');
		}
	}
}
