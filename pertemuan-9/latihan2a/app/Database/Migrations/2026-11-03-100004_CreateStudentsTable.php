<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateStudentsTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'               => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'user_id'          => ['type' => 'INT', 'unsigned' => true],
            'study_program_id' => ['type' => 'INT', 'unsigned' => true],
            'advisor_id'       => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'student_number'   => ['type' => 'VARCHAR', 'constraint' => 20],
            'entry_year'       => ['type' => 'SMALLINT', 'unsigned' => true],
            'created_at'       => ['type' => 'DATETIME', 'null' => true],
            'updated_at'       => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('user_id');
        $this->forge->addUniqueKey('student_number');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('study_program_id', 'study_programs', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('advisor_id', 'lecturers', 'id', 'CASCADE', 'SET NULL');
        $this->forge->createTable('students');
    }

    public function down(): void
    {
        $this->forge->dropTable('students', true);
    }
}
