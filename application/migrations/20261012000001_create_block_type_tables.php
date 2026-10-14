<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_block_type_tables extends CI_Migration
{
	public function up()
	{
		// 1. Create video_blocks table
		$this->dbforge->add_field([
			'id' => [
				'type' => 'INT',
				'constraint' => 10,
				'unsigned' => TRUE,
				'auto_increment' => TRUE,
			],
			'block_id' => [
				'type' => 'INT',
				'constraint' => 10,
				'unsigned' => TRUE,
			],
			'provider' => [
				'type' => 'VARCHAR',
				'constraint' => 50,
				'default' => 'youtube',
			],
			'url' => [
				'type' => 'VARCHAR',
				'constraint' => 500,
			],
			'duration_in_seconds' => [
				'type' => 'INT',
				'constraint' => 10,
				'unsigned' => TRUE,
				'default' => 0,
			],
			'created_at' => [
				'type' => 'DATETIME',
				'null' => FALSE,
			],
			'updated_at' => [
				'type' => 'DATETIME',
				'null' => FALSE,
			],
		]);
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->add_key('block_id');
		$this->dbforge->create_table('video_blocks', TRUE);

		$this->db->query("ALTER TABLE `video_blocks` 
			MODIFY `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, 
			MODIFY `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
		");

		$this->db->query("ALTER TABLE `video_blocks` 
			ADD CONSTRAINT `fk_video_blocks_block_id` 
			FOREIGN KEY (`block_id`) REFERENCES `lesson_blocks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
		");

		// 2. Create text_blocks table
		$this->dbforge->add_field([
			'id' => [
				'type' => 'INT',
				'constraint' => 10,
				'unsigned' => TRUE,
				'auto_increment' => TRUE,
			],
			'block_id' => [
				'type' => 'INT',
				'constraint' => 10,
				'unsigned' => TRUE,
			],
			'content' => [
				'type' => 'LONGTEXT',
			],
			'min_reading_time_in_seconds' => [
				'type' => 'INT',
				'constraint' => 10,
				'unsigned' => TRUE,
				'default' => 0,
			],
			'created_at' => [
				'type' => 'DATETIME',
				'null' => FALSE,
			],
			'updated_at' => [
				'type' => 'DATETIME',
				'null' => FALSE,
			],
		]);
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->add_key('block_id');
		$this->dbforge->create_table('text_blocks', TRUE);

		$this->db->query("ALTER TABLE `text_blocks` 
			MODIFY `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, 
			MODIFY `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
		");

		$this->db->query("ALTER TABLE `text_blocks` 
			ADD CONSTRAINT `fk_text_blocks_block_id` 
			FOREIGN KEY (`block_id`) REFERENCES `lesson_blocks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
		");

		// 3. Create attachment_blocks table
		$this->dbforge->add_field([
			'id' => [
				'type' => 'INT',
				'constraint' => 10,
				'unsigned' => TRUE,
				'auto_increment' => TRUE,
			],
			'block_id' => [
				'type' => 'INT',
				'constraint' => 10,
				'unsigned' => TRUE,
			],
			'filename' => [
				'type' => 'VARCHAR',
				'constraint' => 255,
			],
			'filepath' => [
				'type' => 'VARCHAR',
				'constraint' => 500,
			],
			'file_size' => [
				'type' => 'INT',
				'constraint' => 10,
				'unsigned' => TRUE,
				'default' => 0,
			],
			'file_type' => [
				'type' => 'VARCHAR',
				'constraint' => 100,
				'default' => '',
			],
			'min_reading_time_in_seconds' => [
				'type' => 'INT',
				'constraint' => 10,
				'unsigned' => TRUE,
				'default' => 0,
			],
			'created_at' => [
				'type' => 'DATETIME',
				'null' => FALSE,
			],
			'updated_at' => [
				'type' => 'DATETIME',
				'null' => FALSE,
			],
		]);
		$this->dbforge->add_key('id', TRUE);
		$this->dbforge->add_key('block_id');
		$this->dbforge->create_table('attachment_blocks', TRUE);

		$this->db->query("ALTER TABLE `attachment_blocks` 
			MODIFY `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, 
			MODIFY `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
		");

		$this->db->query("ALTER TABLE `attachment_blocks` 
			ADD CONSTRAINT `fk_attachment_blocks_block_id` 
			FOREIGN KEY (`block_id`) REFERENCES `lesson_blocks` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
		");

		// 4. Migrate any existing rows from lesson_blocks.payload into child tables
		if ($this->db->field_exists('payload', 'lesson_blocks')) {
			$existing_blocks = $this->db->get('lesson_blocks')->result_array();
			foreach ($existing_blocks as $row) {
				$block_id = (int) $row['id'];
				$type = (string) $row['type'];
				$payload = !empty($row['payload']) ? json_decode((string) $row['payload'], true) : [];
				if (!is_array($payload)) {
					$payload = [];
				}

				if ($type === 'video') {
					$this->db->insert('video_blocks', [
						'block_id' => $block_id,
						'provider' => $payload['provider'] ?? 'youtube',
						'url' => $payload['url'] ?? '',
						'duration_in_seconds' => (int) ($payload['duration_in_seconds'] ?? 0),
					]);
				} elseif ($type === 'text') {
					$this->db->insert('text_blocks', [
						'block_id' => $block_id,
						'content' => $payload['content'] ?? '',
						'min_reading_time_in_seconds' => (int) ($payload['min_reading_time_in_seconds'] ?? 0),
					]);
				} elseif ($type === 'attachment') {
					$this->db->insert('attachment_blocks', [
						'block_id' => $block_id,
						'filename' => $payload['filename'] ?? '',
						'filepath' => $payload['filepath'] ?? '',
						'file_size' => (int) ($payload['file_size'] ?? 0),
						'file_type' => $payload['file_type'] ?? '',
						'min_reading_time_in_seconds' => (int) ($payload['min_reading_time_in_seconds'] ?? 0),
					]);
				}
			}

			// Drop payload column from lesson_blocks
			$this->dbforge->drop_column('lesson_blocks', 'payload');
		}
	}

	public function down()
	{
		// Restore payload column if needed
		if (!$this->db->field_exists('payload', 'lesson_blocks')) {
			$this->dbforge->add_column('lesson_blocks', [
				'payload' => [
					'type' => 'JSON',
					'null' => TRUE,
				],
			]);
		}

		$this->dbforge->drop_table('attachment_blocks', TRUE);
		$this->dbforge->drop_table('text_blocks', TRUE);
		$this->dbforge->drop_table('video_blocks', TRUE);
	}
}
