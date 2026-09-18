<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Remove_educational_core extends CI_Migration
{

	public function up()
	{
		$this->dbforge->drop_table('class_lessons', TRUE);
		$this->dbforge->drop_table('lessons', TRUE);
		$this->dbforge->drop_table('sections', TRUE);
		$this->dbforge->drop_table('modules', TRUE);
		$this->dbforge->drop_table('classes', TRUE);
		$this->dbforge->drop_table('courses', TRUE);
		$this->dbforge->drop_table('categories', TRUE);
		$this->dbforge->drop_table('video_providers', TRUE);
	}

	public function down()
	{
		// Down migration is omitted intentionally since this is a cleanup
	}
}
