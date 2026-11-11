<?php

namespace Tests\Feature;

use App\Models\OrderModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use Tests\Support\ShopTestCase;

/**
 * Pembatasan akses berdasarkan role, termasuk saat URL diakses langsung (FR-004, BR-006, BR-007, AC-10, AC-14).
 *
 * @internal
 */
final class AccessControlTest extends ShopTestCase
{
    public function testGuestIsRedirectedToLoginForPrivatePages(): void
    {
        foreach (['cart', 'checkout', 'orders', 'admin', 'admin/orders'] as $url) {
            $this->get($url)->assertRedirectTo(url_to('login'));
        }
    }

    public function testCustomerCannotOpenAdminPanel(): void
    {
        $this->actingAs('budi@example.test')->get('admin')->assertStatus(403);
        $this->actingAs('budi@example.test')->get('admin/products')->assertStatus(403);
        $this->actingAs('budi@example.test')->get('admin/orders')->assertStatus(403);
    }

    public function testOperatorCanManageOrdersButNotCatalogOrPayments(): void
    {
        $this->actingAs('operator@shopease.test')->get('admin/orders')->assertOK();
        $this->actingAs('operator@shopease.test')->get('admin')->assertStatus(403);
        $this->actingAs('operator@shopease.test')->get('admin/products')->assertStatus(403);

        $order = model(OrderModel::class)->firstWhere(['order_status' => 'waiting_verification']);
        $this->actingAs('operator@shopease.test')->post('admin/orders/' . $order['id'] . '/verify', ['decision' => 'approve'])->assertStatus(403);
        $this->seeInStore('orders', ['id' => $order['id'], 'order_status' => 'waiting_verification']);
    }

    public function testAdminCanOpenEveryAdminPage(): void
    {
        foreach (['admin', 'admin/orders', 'admin/products', 'admin/products/new', 'admin/categories', 'admin/users', 'admin/reports'] as $url) {
            $this->actingAs('admin@shopease.test')->get($url)->assertOK();
        }
    }

    public function testAdminAndOperatorCannotUseCustomerCart(): void
    {
        $this->actingAs('admin@shopease.test')->get('cart')->assertStatus(403);
        $this->actingAs('operator@shopease.test')->get('checkout')->assertStatus(403);
    }

    public function testCustomerCannotSeeAnotherCustomersOrder(): void
    {
        $budi      = model(\App\Models\UserModel::class)->findByEmail('budi@example.test');
        $budiOrder = model(OrderModel::class)->firstWhere(['user_id' => $budi['id']]);

        $this->actingAs('budi@example.test')->get('orders/' . $budiOrder['order_number'])->assertOK();

        $this->expectException(PageNotFoundException::class);
        $this->actingAs('siti@example.test')->get('orders/' . $budiOrder['order_number']);
    }
}
