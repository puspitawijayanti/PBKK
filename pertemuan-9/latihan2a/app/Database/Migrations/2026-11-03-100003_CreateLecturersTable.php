<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLecturersTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'               => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'user_id'          => ['type' => 'INT', 'unsigned' => true],
            'study_program_id' => ['type' => 'INT', 'unsigned' => true],
            'lecturer_number'  => ['type' => 'VARCHAR', 'constraint' => 20],
            'email'            => ['type' => 'VARCHAR', 'constraint' => 120, 'null' => true],
            'phone'            => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'created_at'       => ['type' => 'DATETIME', 'null' => true],
            'updated_at'       => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('user_id');
        $this->forge->addUniqueKey('lecturer_number');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('study_program_id', 'study_programs', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('lecturers');
    }

    public function down(): void
    {
        $this->forge->dropTable('lecturers', true);
    }
}
