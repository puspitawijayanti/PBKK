<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateOrdersTables extends Migration
{
    public function up()
    {
        // orders: header transaksi
        $this->forge->addField([
            'id'               => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'user_id'          => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'address_id'       => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'order_number'     => ['type' => 'VARCHAR', 'constraint' => 50],
            'order_date'       => ['type' => 'DATETIME'],
            'subtotal'         => ['type' => 'DECIMAL', 'constraint' => '12,2', 'default' => 0],
            'shipping_cost'    => ['type' => 'DECIMAL', 'constraint' => '12,2', 'default' => 0],
            'total'            => ['type' => 'DECIMAL', 'constraint' => '12,2', 'default' => 0],
            // pending_payment | waiting_verification | processing | shipped | completed | cancelled
            'order_status'     => ['type' => 'VARCHAR', 'constraint' => 30, 'default' => 'pending_payment'],
            // snapshot alamat saat transaksi supaya riwayat tidak berubah jika alamat diedit
            'shipping_address' => ['type' => 'TEXT', 'null' => true],
            'notes'            => ['type' => 'TEXT', 'null' => true],
            'created_at'       => ['type' => 'DATETIME', 'null' => true],
            'updated_at'       => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('order_number');
        $this->forge->addKey('user_id');
        $this->forge->addKey('order_status');
        $this->forge->addKey('order_date');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('address_id', 'addresses', 'id', 'CASCADE', 'SET NULL');
        $this->forge->createTable('orders');

        // order_items: rincian + snapshot nama & harga (BR-004)
        $this->forge->addField([
            'id'           => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'order_id'     => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'product_id'   => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'product_name' => ['type' => 'VARCHAR', 'constraint' => 150],
            'price'        => ['type' => 'DECIMAL', 'constraint' => '12,2'],
            'quantity'     => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'subtotal'     => ['type' => 'DECIMAL', 'constraint' => '12,2'],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('order_id');
        $this->forge->addKey('product_id');
        $this->forge->addForeignKey('order_id', 'orders', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('product_id', 'products', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('order_items');

        // order_status_logs: jejak perubahan status (BR-012)
        $this->forge->addField([
            'id'         => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'order_id'   => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'old_status' => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true],
            'new_status' => ['type' => 'VARCHAR', 'constraint' => 30],
            'changed_by' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'note'       => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addKey('order_id');
        $this->forge->addForeignKey('order_id', 'orders', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('changed_by', 'users', 'id', 'CASCADE', 'SET NULL');
        $this->forge->createTable('order_status_logs');
    }

    public function down()
    {
        $this->forge->dropTable('order_status_logs', true);
        $this->forge->dropTable('order_items', true);
        $this->forge->dropTable('orders', true);
    }
}
