<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Seed_course_and_category_permissions extends CI_Migration
{
	public function up()
	{
		$permissions = [
			['name' => 'Visualizar Cursos', 'slug' => 'courses.view', 'description' => 'Permite visualizar o catálogo de cursos'],
			['name' => 'Criar Curso', 'slug' => 'courses.create', 'description' => 'Permite cadastrar novos cursos'],
			['name' => 'Editar Curso', 'slug' => 'courses.edit', 'description' => 'Permite editar cursos existentes'],
			['name' => 'Excluir Curso', 'slug' => 'courses.delete', 'description' => 'Permite excluir ou arquivar cursos'],
			['name' => 'Visualizar Categorias', 'slug' => 'categories.view', 'description' => 'Permite visualizar as categorias pedagógicas'],
			['name' => 'Criar Categoria', 'slug' => 'categories.create', 'description' => 'Permite cadastrar novas categorias'],
			['name' => 'Editar Categoria', 'slug' => 'categories.edit', 'description' => 'Permite editar categorias existentes'],
			['name' => 'Excluir Categoria', 'slug' => 'categories.delete', 'description' => 'Permite excluir categorias sem vínculos'],
		];

		foreach ($permissions as $perm) {
			$existing = $this->db->get_where('permissions', ['slug' => $perm['slug']])->row();
			if ($existing) {
				$perm_id = $existing->id;
			} else {
				$this->db->insert('permissions', $perm);
				$perm_id = $this->db->insert_id();
			}

			// Link to Admin role (id = 2) if not already linked
			$role_perm = $this->db->get_where('role_permissions', [
				'role_id' => 2,
				'permission_id' => $perm_id,
			])->row();

			if (!$role_perm) {
				$this->db->insert('role_permissions', [
					'role_id' => 2,
					'permission_id' => $perm_id,
				]);
			}
		}
	}

	public function down()
	{
		$slugs = [
			'courses.view',
			'courses.create',
			'courses.edit',
			'courses.delete',
			'categories.view',
			'categories.create',
			'categories.edit',
			'categories.delete',
		];

		$this->db->where_in('slug', $slugs);
		$perms = $this->db->get('permissions')->result_array();

		if (!empty($perms)) {
			$perm_ids = array_column($perms, 'id');
			$this->db->where_in('permission_id', $perm_ids)->delete('role_permissions');
			$this->db->where_in('id', $perm_ids)->delete('permissions');
		}
	}
}
