<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Create_educational_core extends CI_Migration {

    public function up()
    {
        // 1. video_providers
        $this->dbforge->add_field([
            'id' => ['type' => 'INT', 'unsigned' => TRUE, 'auto_increment' => TRUE],
            'name' => ['type' => 'VARCHAR', 'constraint' => '255'],
            'driver' => ['type' => 'VARCHAR', 'constraint' => '50'], // e.g. youtube, vimeo
            'credentials' => ['type' => 'JSON', 'null' => TRUE],
            'created_at' => ['type' => 'DATETIME', 'null' => TRUE],
            'updated_at' => ['type' => 'DATETIME', 'null' => TRUE],
            'deleted_at' => ['type' => 'DATETIME', 'null' => TRUE],
        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('video_providers', TRUE);

        // 2. categories
        $this->dbforge->add_field([
            'id' => ['type' => 'INT', 'unsigned' => TRUE, 'auto_increment' => TRUE],
            'name' => ['type' => 'VARCHAR', 'constraint' => '255'],
            'slug' => ['type' => 'VARCHAR', 'constraint' => '255'],
            'created_at' => ['type' => 'DATETIME', 'null' => TRUE],
            'updated_at' => ['type' => 'DATETIME', 'null' => TRUE],
            'deleted_at' => ['type' => 'DATETIME', 'null' => TRUE],
        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('categories', TRUE);

        // 3. courses
        $this->dbforge->add_field([
            'id' => ['type' => 'INT', 'unsigned' => TRUE, 'auto_increment' => TRUE],
            'category_id' => ['type' => 'INT', 'unsigned' => TRUE, 'null' => TRUE],
            'title' => ['type' => 'VARCHAR', 'constraint' => '255'],
            'description' => ['type' => 'TEXT', 'null' => TRUE],
            'base_price' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'default' => '0.00'],
            'created_at' => ['type' => 'DATETIME', 'null' => TRUE],
            'updated_at' => ['type' => 'DATETIME', 'null' => TRUE],
            'deleted_at' => ['type' => 'DATETIME', 'null' => TRUE],
        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('courses', TRUE);
        $this->db->query("ALTER TABLE courses ADD CONSTRAINT fk_courses_category FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL ON UPDATE CASCADE");

        // 4. classes (Turmas)
        $this->dbforge->add_field([
            'id' => ['type' => 'INT', 'unsigned' => TRUE, 'auto_increment' => TRUE],
            'course_id' => ['type' => 'INT', 'unsigned' => TRUE],
            'name' => ['type' => 'VARCHAR', 'constraint' => '255'],
            'modality' => ['type' => 'ENUM', 'constraint' => ["online", "live", "in_person"], 'default' => 'online'],
            'price' => ['type' => 'DECIMAL', 'constraint' => '10,2', 'null' => TRUE], // NULL inherits from course base_price
            'enrollment_start' => ['type' => 'DATETIME', 'null' => TRUE],
            'enrollment_end' => ['type' => 'DATETIME', 'null' => TRUE],
            'max_students' => ['type' => 'INT', 'null' => TRUE],
            'total_duration_seconds' => ['type' => 'INT', 'default' => 0],
            'created_at' => ['type' => 'DATETIME', 'null' => TRUE],
            'updated_at' => ['type' => 'DATETIME', 'null' => TRUE],
            'deleted_at' => ['type' => 'DATETIME', 'null' => TRUE],
        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('classes', TRUE);
        $this->db->query("ALTER TABLE classes ADD CONSTRAINT fk_classes_course FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE ON UPDATE CASCADE");

        // 5. modules
        $this->dbforge->add_field([
            'id' => ['type' => 'INT', 'unsigned' => TRUE, 'auto_increment' => TRUE],
            'course_id' => ['type' => 'INT', 'unsigned' => TRUE],
            'title' => ['type' => 'VARCHAR', 'constraint' => '255'],
            'sort_order' => ['type' => 'INT', 'default' => 0],
            'created_at' => ['type' => 'DATETIME', 'null' => TRUE],
            'updated_at' => ['type' => 'DATETIME', 'null' => TRUE],
            'deleted_at' => ['type' => 'DATETIME', 'null' => TRUE],
        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('modules', TRUE);
        $this->db->query("ALTER TABLE modules ADD CONSTRAINT fk_modules_course FOREIGN KEY (course_id) REFERENCES courses(id) ON DELETE CASCADE ON UPDATE CASCADE");

        // 6. sections
        $this->dbforge->add_field([
            'id' => ['type' => 'INT', 'unsigned' => TRUE, 'auto_increment' => TRUE],
            'module_id' => ['type' => 'INT', 'unsigned' => TRUE],
            'title' => ['type' => 'VARCHAR', 'constraint' => '255'],
            'sort_order' => ['type' => 'INT', 'default' => 0],
            'created_at' => ['type' => 'DATETIME', 'null' => TRUE],
            'updated_at' => ['type' => 'DATETIME', 'null' => TRUE],
            'deleted_at' => ['type' => 'DATETIME', 'null' => TRUE],
        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('sections', TRUE);
        $this->db->query("ALTER TABLE sections ADD CONSTRAINT fk_sections_module FOREIGN KEY (module_id) REFERENCES modules(id) ON DELETE CASCADE ON UPDATE CASCADE");

        // 7. lessons
        $this->dbforge->add_field([
            'id' => ['type' => 'INT', 'unsigned' => TRUE, 'auto_increment' => TRUE],
            'section_id' => ['type' => 'INT', 'unsigned' => TRUE],
            'video_provider_id' => ['type' => 'INT', 'unsigned' => TRUE, 'null' => TRUE],
            'title' => ['type' => 'VARCHAR', 'constraint' => '255'],
            'video_identifier' => ['type' => 'VARCHAR', 'constraint' => '255', 'null' => TRUE],
            'duration_seconds' => ['type' => 'INT', 'default' => 0],
            'content' => ['type' => 'TEXT', 'null' => TRUE],
            'sort_order' => ['type' => 'INT', 'default' => 0],
            'created_at' => ['type' => 'DATETIME', 'null' => TRUE],
            'updated_at' => ['type' => 'DATETIME', 'null' => TRUE],
            'deleted_at' => ['type' => 'DATETIME', 'null' => TRUE],
        ]);
        $this->dbforge->add_key('id', TRUE);
        $this->dbforge->create_table('lessons', TRUE);
        $this->db->query("ALTER TABLE lessons ADD CONSTRAINT fk_lessons_section FOREIGN KEY (section_id) REFERENCES sections(id) ON DELETE CASCADE ON UPDATE CASCADE");
        $this->db->query("ALTER TABLE lessons ADD CONSTRAINT fk_lessons_provider FOREIGN KEY (video_provider_id) REFERENCES video_providers(id) ON DELETE SET NULL ON UPDATE CASCADE");

        // 8. class_lessons (Curriculum mapping)
        $this->dbforge->add_field([
            'class_id' => ['type' => 'INT', 'unsigned' => TRUE],
            'lesson_id' => ['type' => 'INT', 'unsigned' => TRUE],
        ]);
        $this->dbforge->add_key(['class_id', 'lesson_id'], TRUE);
        $this->dbforge->create_table('class_lessons', TRUE);
        $this->db->query("ALTER TABLE class_lessons ADD CONSTRAINT fk_class_lessons_class FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE CASCADE ON UPDATE CASCADE");
        $this->db->query("ALTER TABLE class_lessons ADD CONSTRAINT fk_class_lessons_lesson FOREIGN KEY (lesson_id) REFERENCES lessons(id) ON DELETE CASCADE ON UPDATE CASCADE");
    }

    public function down()
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
}
