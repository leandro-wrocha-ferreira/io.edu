<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_course_content_foreign_keys extends CI_Migration
{
	public function up()
	{
		$this->db->query('ALTER TABLE `course_modules` ADD CONSTRAINT `fk_course_modules_course_id` FOREIGN KEY (`course_id`) REFERENCES `courses`(`id`) ON DELETE CASCADE ON UPDATE CASCADE');
		$this->db->query('ALTER TABLE `course_lessons` ADD CONSTRAINT `fk_course_lessons_module_id` FOREIGN KEY (`module_id`) REFERENCES `course_modules`(`id`) ON DELETE CASCADE ON UPDATE CASCADE');
		$this->db->query('ALTER TABLE `lesson_blocks` ADD CONSTRAINT `fk_lesson_blocks_lesson_id` FOREIGN KEY (`lesson_id`) REFERENCES `course_lessons`(`id`) ON DELETE CASCADE ON UPDATE CASCADE');
	}

	public function down()
	{
		$this->db->query('ALTER TABLE `lesson_blocks` DROP FOREIGN KEY `fk_lesson_blocks_lesson_id`');
		$this->db->query('ALTER TABLE `course_lessons` DROP FOREIGN KEY `fk_course_lessons_module_id`');
		$this->db->query('ALTER TABLE `course_modules` DROP FOREIGN KEY `fk_course_modules_course_id`');
	}
}
