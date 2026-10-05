<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_courses_foreign_keys extends CI_Migration
{
	public function up()
	{
		$this->db->query('ALTER TABLE `courses` ADD CONSTRAINT `fk_courses_category_id` FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE RESTRICT ON UPDATE CASCADE');
	}

	public function down()
	{
		$this->db->query('ALTER TABLE `courses` DROP FOREIGN KEY `fk_courses_category_id`');
	}
}
