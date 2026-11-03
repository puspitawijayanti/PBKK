<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateToolsTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'category_id' => ['type' => 'INT', 'unsigned' => true],
            'name'        => ['type' => 'VARCHAR', 'constraint' => 120],
            'slug'        => ['type' => 'VARCHAR', 'constraint' => 140],
            'stock'       => ['type' => 'INT', 'unsigned' => true, 'default' => 0],
            'condition'   => ['type' => 'ENUM', 'constraint' => ['good', 'fair', 'damaged'], 'default' => 'good'],
            'photo'       => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'description' => ['type' => 'TEXT', 'null' => true],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
            'updated_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('slug');
        $this->forge->addForeignKey('category_id', 'categories', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('tools');
    }

    public function down(): void
    {
        $this->forge->dropTable('tools', true);
    }
}
