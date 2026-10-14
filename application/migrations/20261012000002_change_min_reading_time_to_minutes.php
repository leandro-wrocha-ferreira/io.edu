<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Change_min_reading_time_to_minutes extends CI_Migration
{
	public function up()
	{
		if ($this->db->field_exists('min_reading_time_in_seconds', 'text_blocks')) {
			$this->db->query("ALTER TABLE `text_blocks` CHANGE COLUMN `min_reading_time_in_seconds` `min_reading_time_in_minutes` INT UNSIGNED DEFAULT 0");
		}

		if ($this->db->field_exists('min_reading_time_in_seconds', 'attachment_blocks')) {
			$this->db->query("ALTER TABLE `attachment_blocks` CHANGE COLUMN `min_reading_time_in_seconds` `min_reading_time_in_minutes` INT UNSIGNED DEFAULT 0");
		}
	}

	public function down()
	{
		if ($this->db->field_exists('min_reading_time_in_minutes', 'text_blocks')) {
			$this->db->query("ALTER TABLE `text_blocks` CHANGE COLUMN `min_reading_time_in_minutes` `min_reading_time_in_seconds` INT UNSIGNED DEFAULT 0");
		}

		if ($this->db->field_exists('min_reading_time_in_minutes', 'attachment_blocks')) {
			$this->db->query("ALTER TABLE `attachment_blocks` CHANGE COLUMN `min_reading_time_in_minutes` `min_reading_time_in_seconds` INT UNSIGNED DEFAULT 0");
		}
	}
}
