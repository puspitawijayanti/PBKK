<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateStudyProgramsTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'code'       => ['type' => 'VARCHAR', 'constraint' => 10],
            'name'       => ['type' => 'VARCHAR', 'constraint' => 100],
            'degree'     => ['type' => 'ENUM', 'constraint' => ['D3', 'D4', 'S1', 'S2'], 'default' => 'S1'],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('code');
        $this->forge->createTable('study_programs');
    }

    public function down(): void
    {
        $this->forge->dropTable('study_programs', true);
    }
}
