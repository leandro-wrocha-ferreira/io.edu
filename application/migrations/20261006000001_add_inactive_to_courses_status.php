<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_inactive_to_courses_status extends CI_Migration
{
	public function up()
	{
		$this->db->query("ALTER TABLE `courses` MODIFY `status` ENUM('draft','active','inactive','archived') NOT NULL DEFAULT 'draft'");
	}

	public function down()
	{
		$this->db->query("ALTER TABLE `courses` MODIFY `status` ENUM('draft','active','archived') NOT NULL DEFAULT 'draft'");
	}
}
