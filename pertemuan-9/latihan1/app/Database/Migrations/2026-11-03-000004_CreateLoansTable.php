<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateLoansTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'user_id'     => ['type' => 'INT', 'unsigned' => true],
            'tool_id'     => ['type' => 'INT', 'unsigned' => true],
            'quantity'    => ['type' => 'INT', 'unsigned' => true, 'default' => 1],
            'borrow_date' => ['type' => 'DATE'],
            'return_date' => ['type' => 'DATE'],
            'returned_at' => ['type' => 'DATETIME', 'null' => true],
            'status'      => ['type' => 'ENUM', 'constraint' => ['pending', 'approved', 'borrowed', 'returned', 'rejected'], 'default' => 'pending'],
            'note'        => ['type' => 'TEXT', 'null' => true],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
            'updated_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('status');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('tool_id', 'tools', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('loans');
    }

    public function down(): void
    {
        $this->forge->dropTable('loans', true);
    }
}
