<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_is_available_for_sale_to_courses extends CI_Migration
{
	public function up()
	{
		$fields = [
			'is_available_for_sale' => [
				'type' => 'TINYINT',
				'constraint' => 1,
				'default' => 0,
				'null' => FALSE,
				'after' => 'status',
			],
		];

		$this->dbforge->add_column('courses', $fields);
		$this->db->query("ALTER TABLE `courses` ADD INDEX `idx_courses_sales_eligibility` (`status`, `is_available_for_sale`)");
	}

	public function down()
	{
		$this->db->query("ALTER TABLE `courses` DROP INDEX `idx_courses_sales_eligibility`");
		$this->dbforge->drop_column('courses', 'is_available_for_sale');
	}
}
