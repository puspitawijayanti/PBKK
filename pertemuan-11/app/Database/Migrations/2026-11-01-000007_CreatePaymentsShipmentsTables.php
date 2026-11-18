<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePaymentsShipmentsTables extends Migration
{
    public function up()
    {
        // payments: pembayaran manual (upload bukti transfer)
        $this->forge->addField([
            'id'             => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'order_id'       => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'method'         => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'amount'         => ['type' => 'DECIMAL', 'constraint' => '12,2'],
            'proof_file'     => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            // unpaid | submitted | verified | rejected  (terpisah dari status pesanan, BR-008)
            'payment_status' => ['type' => 'VARCHAR', 'constraint' => 20, 'default' => 'unpaid'],
            'note'           => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'verified_by'    => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'verified_at'    => ['type' => 'DATETIME', 'null' => true],
            'created_at'     => ['type' => 'DATETIME', 'null' => true],
            'updated_at'     => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('order_id');
        $this->forge->addKey('payment_status');
        $this->forge->addForeignKey('order_id', 'orders', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('verified_by', 'users', 'id', 'CASCADE', 'SET NULL');
        $this->forge->createTable('payments');

        // shipments: pengiriman
        $this->forge->addField([
            'id'              => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'order_id'        => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'courier'         => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'tracking_number' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            // pending | shipped | delivered
            'shipment_status' => ['type' => 'VARCHAR', 'constraint' => 30, 'default' => 'pending'],
            'shipped_at'      => ['type' => 'DATETIME', 'null' => true],
            'delivered_at'    => ['type' => 'DATETIME', 'null' => true],
            'created_at'      => ['type' => 'DATETIME', 'null' => true],
            'updated_at'      => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('order_id');
        $this->forge->addKey('tracking_number');
        $this->forge->addForeignKey('order_id', 'orders', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('shipments');
    }

    public function down()
    {
        $this->forge->dropTable('shipments', true);
        $this->forge->dropTable('payments', true);
    }
}
