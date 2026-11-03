<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProductsTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'          => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'category_id' => ['type' => 'INT', 'unsigned' => true],
            'name'        => ['type' => 'VARCHAR', 'constraint' => 120],
            'slug'        => ['type' => 'VARCHAR', 'constraint' => 140],
            'origin'      => ['type' => 'VARCHAR', 'constraint' => 100],
            'process'     => ['type' => 'ENUM', 'constraint' => ['washed', 'natural', 'honey', 'wet_hulled'], 'default' => 'washed'],
            'roast_level' => ['type' => 'ENUM', 'constraint' => ['light', 'medium', 'dark'], 'default' => 'medium'],
            'weight_gram' => ['type' => 'SMALLINT', 'unsigned' => true, 'default' => 250],
            'price'       => ['type' => 'INT', 'unsigned' => true],
            'stock'       => ['type' => 'INT', 'unsigned' => true, 'default' => 0],
            'photo'       => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'description' => ['type' => 'TEXT', 'null' => true],
            'is_active'   => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
            'updated_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('slug');
        $this->forge->addKey('price');
        $this->forge->addForeignKey('category_id', 'categories', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('products');
    }

    public function down(): void
    {
        $this->forge->dropTable('products', true);
    }
}
