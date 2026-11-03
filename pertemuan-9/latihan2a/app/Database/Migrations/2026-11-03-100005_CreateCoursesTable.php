<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCoursesTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'               => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'study_program_id' => ['type' => 'INT', 'unsigned' => true],
            'lecturer_id'      => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'code'             => ['type' => 'VARCHAR', 'constraint' => 15],
            'name'             => ['type' => 'VARCHAR', 'constraint' => 120],
            'credits'          => ['type' => 'TINYINT', 'unsigned' => true],
            'semester'         => ['type' => 'TINYINT', 'unsigned' => true],
            'quota'            => ['type' => 'SMALLINT', 'unsigned' => true, 'default' => 40],
            'schedule'         => ['type' => 'VARCHAR', 'constraint' => 60, 'null' => true],
            'room'             => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true],
            'created_at'       => ['type' => 'DATETIME', 'null' => true],
            'updated_at'       => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('code');
        $this->forge->addForeignKey('study_program_id', 'study_programs', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('lecturer_id', 'lecturers', 'id', 'CASCADE', 'SET NULL');
        $this->forge->createTable('courses');
    }

    public function down(): void
    {
        $this->forge->dropTable('courses', true);
    }
}
