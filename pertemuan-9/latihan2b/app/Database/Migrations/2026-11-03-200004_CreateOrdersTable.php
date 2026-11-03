<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateOrdersTable extends Migration
{
    public function up(): void
    {
        $this->forge->addField([
            'id'              => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'user_id'         => ['type' => 'INT', 'unsigned' => true],
            'code'            => ['type' => 'VARCHAR', 'constraint' => 30],
            'status'          => ['type' => 'ENUM', 'constraint' => ['awaiting_payment', 'awaiting_verification', 'processing', 'shipped', 'completed', 'cancelled'], 'default' => 'awaiting_payment'],
            'recipient_name'  => ['type' => 'VARCHAR', 'constraint' => 100],
            'phone'           => ['type' => 'VARCHAR', 'constraint' => 20],
            'address'         => ['type' => 'TEXT'],
            'city'            => ['type' => 'VARCHAR', 'constraint' => 60],
            'postal_code'     => ['type' => 'VARCHAR', 'constraint' => 10],
            'note'            => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'subtotal'        => ['type' => 'INT', 'unsigned' => true],
            'shipping_cost'   => ['type' => 'INT', 'unsigned' => true],
            'total'           => ['type' => 'INT', 'unsigned' => true],
            'payment_proof'   => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'courier'         => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true],
            'tracking_number' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'paid_at'         => ['type' => 'DATETIME', 'null' => true],
            'verified_at'     => ['type' => 'DATETIME', 'null' => true],
            'shipped_at'      => ['type' => 'DATETIME', 'null' => true],
            'completed_at'    => ['type' => 'DATETIME', 'null' => true],
            'cancelled_at'    => ['type' => 'DATETIME', 'null' => true],
            'created_at'      => ['type' => 'DATETIME', 'null' => true],
            'updated_at'      => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('code');
        $this->forge->addKey('status');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('orders');
    }

    public function down(): void
    {
        $this->forge->dropTable('orders', true);
    }
}
