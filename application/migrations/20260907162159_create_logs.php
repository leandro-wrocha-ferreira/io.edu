<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_logs extends CI_Migration
{
	public function up()
	{
		$this->dbforge->add_field([
			'id' => [
				'type'           => 'INT',
				'constraint'     => 11,
				'unsigned'       => TRUE,
				'auto_increment' => TRUE
			],
			'user_execute' => [
				'type'       => 'INT',
				'constraint' => 11,
				'unsigned'   => TRUE,
				'null'       => TRUE
			],
			'table' => [
				'type'       => 'VARCHAR',
				'constraint' => 100,
				'null'       => FALSE
			],
			'row' => [
				'type'       => 'VARCHAR',
				'constraint' => 255,
				'null'       => TRUE
			],
			'content' => [
				'type' => 'JSON',
				'null' => TRUE
			],
			'created_at' => [
				'type' => 'DATETIME',
				'null' => TRUE
			]
		]);
		
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->create_table('logs');

		// Adicionando trigger para o created_at como estipulado nas diretrizes do projeto
		$trigger = "
			CREATE TRIGGER trg_logs_before_insert
			BEFORE INSERT ON logs
			FOR EACH ROW
			BEGIN
				IF NEW.created_at IS NULL THEN
					SET NEW.created_at = CURRENT_TIMESTAMP;
				END IF;
			END;
		";
		$this->db->query($trigger);
	}

	public function down()
	{
		$this->db->query("DROP TRIGGER IF EXISTS trg_logs_before_insert");
		$this->dbforge->drop_table('logs');
	}
}
