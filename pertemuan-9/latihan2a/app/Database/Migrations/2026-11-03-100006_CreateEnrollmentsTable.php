<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateEnrollmentsTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'            => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'student_id'    => ['type' => 'INT', 'unsigned' => true],
            'course_id'     => ['type' => 'INT', 'unsigned' => true],
            'academic_year' => ['type' => 'CHAR', 'constraint' => 9],
            'term'          => ['type' => 'ENUM', 'constraint' => ['odd', 'even']],
            'score'         => ['type' => 'DECIMAL', 'constraint' => '5,2', 'null' => true],
            'grade'         => ['type' => 'VARCHAR', 'constraint' => 2, 'null' => true],
            'weight'        => ['type' => 'DECIMAL', 'constraint' => '3,2', 'null' => true],
            'created_at'    => ['type' => 'DATETIME', 'null' => true],
            'updated_at'    => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey(['student_id', 'course_id', 'academic_year'], 'enrollments_student_course_year');
        $this->forge->addKey(['course_id', 'academic_year', 'term']);
        $this->forge->addForeignKey('student_id', 'students', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('course_id', 'courses', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('enrollments');
    }

    public function down(): void
    {
        $this->forge->dropTable('enrollments', true);
    }
}
