<?php

namespace Tests\Feature;

use App\Models\AddressModel;
use App\Models\CartItemModel;
use App\Models\OrderModel;
use App\Models\ProductModel;
use App\Models\UserModel;
use App\Services\OrderService;
use Tests\Support\ShopTestCase;

/**
 * Alur belanja dari keranjang sampai pesanan selesai (AC-06 s.d. AC-09, AC-11, AC-12).
 *
 * @internal
 */
final class ShoppingFlowTest extends ShopTestCase
{
    private const CUSTOMER = 'andi@example.test';

    public function testCustomerCanAddToCartAndChangeQuantity(): void
    {
        $product = $this->productBySlug('matras-yoga-6mm');

        $this->actingAs(self::CUSTOMER)->post('cart', ['product_id' => $product['id'], 'quantity' => 2])
            ->assertSessionHas('success');

        $item = $this->cartItem($product['id']);
        $this->assertSame(2, (int) $item['quantity']);

        $this->actingAs(self::CUSTOMER)->post('cart/' . $item['id'], ['quantity' => 3]);
        $this->assertSame(3, (int) $this->cartItem($product['id'])['quantity']);

        $this->actingAs(self::CUSTOMER)->get('cart')->assertSee('Matras Yoga 6 mm');
    }

    public function testCartRejectsQuantityAboveStock(): void
    {
        $product = $this->productBySlug('sunscreen-spf-50');

        $this->actingAs(self::CUSTOMER)->post('cart', ['product_id' => $product['id'], 'quantity' => $product['stock'] + 1])
            ->assertSessionHas('error');

        $this->assertNull($this->cartItem($product['id']));
    }

    public function testCartRejectsInactiveProduct(): void
    {
        $product = $this->productBySlug('topi-baseball-polos');

        $this->actingAs(self::CUSTOMER)->post('cart', ['product_id' => $product['id'], 'quantity' => 1])
            ->assertSessionHas('error');
    }

    public function testCheckoutCreatesOrderWithPriceSnapshotAndReducesStock(): void
    {
        $matras = $this->productBySlug('matras-yoga-6mm');
        $botol  = $this->productBySlug('botol-minum-stainless');
        $this->addToCart($matras['id'], 2);
        $this->addToCart($botol['id'], 1);

        $result = $this->checkout();
        $order  = $this->latestOrder();

        $result->assertRedirectTo(url_to('order.show', $order['order_number']));
        $this->assertSame('pending_payment', $order['order_status']);

        // Total dihitung di backend: (159.000 x 2) + 99.000 + ongkir 15.000
        $this->assertEqualsWithDelta(417000, (float) $order['subtotal'], 0.01);
        $this->assertEqualsWithDelta(432000, (float) $order['total'], 0.01);
        $this->seeInStore('order_items', ['order_id' => $order['id'], 'product_name' => 'Matras Yoga 6 mm', 'quantity' => 2]);
        $this->seeInStore('payments', ['order_id' => $order['id'], 'payment_status' => 'unpaid']);

        $this->assertSame($matras['stock'] - 2, (int) $this->productBySlug('matras-yoga-6mm')['stock']);
        $this->assertNull($this->cartItem($matras['id']), 'Keranjang dikosongkan setelah checkout.');

        // Snapshot harga tidak berubah meskipun harga produk dinaikkan.
        model(ProductModel::class)->update($matras['id'], ['price' => 999000.0]);
        $this->actingAs(self::CUSTOMER)->get('orders/' . $order['order_number'])->assertSee('Rp159.000');
    }

    public function testCheckoutIsRejectedWhenStockRunsOut(): void
    {
        $product = $this->productBySlug('sunscreen-spf-50');
        $this->addToCart($product['id'], 2);
        model(ProductModel::class)->update($product['id'], ['stock' => 1]);

        $before = model(OrderModel::class)->count();
        $this->checkout()->assertSessionHas('error');

        $this->assertSame($before, model(OrderModel::class)->count());
        $this->assertSame(1, (int) $this->productBySlug('sunscreen-spf-50')['stock']);
    }

    public function testCheckoutTokenPreventsDoubleSubmit(): void
    {
        $this->addToCart($this->productBySlug('set-pulpen-gel')['id'], 1);
        $before = model(OrderModel::class)->count();

        $this->checkout();
        // Kirim ulang form yang sama: token di session sudah terpakai sehingga ditolak.
        $this->addToCart($this->productBySlug('set-pulpen-gel')['id'], 1);
        $this->checkout('token-uji', null)->assertSessionHas('info');

        $this->assertSame($before + 1, model(OrderModel::class)->count());
    }

    public function testFullOrderLifecycleFromPaymentToCompletion(): void
    {
        $this->addToCart($this->productBySlug('tote-bag-kanvas')['id'], 1);
        $this->checkout();
        $order = $this->latestOrder();
        $users = model(UserModel::class);

        // Customer mengunggah bukti (unggah file diuji lewat service; validasi file ada di controller).
        (new OrderService())->submitPayment($order, $users->findByEmail(self::CUSTOMER)['id'], 'bukti-uji.png');
        $this->seeInStore('orders', ['id' => $order['id'], 'order_status' => 'waiting_verification']);
        $this->seeInStore('payments', ['order_id' => $order['id'], 'payment_status' => 'submitted']);

        // Admin menolak tanpa alasan -> gagal; dengan alasan -> kembali menunggu pembayaran.
        $this->actingAs('admin@shopease.test')->post('admin/orders/' . $order['id'] . '/verify', ['decision' => 'reject'])->assertSessionHas('error');
        $this->actingAs('admin@shopease.test')->post('admin/orders/' . $order['id'] . '/verify', ['decision' => 'reject', 'note' => 'Nominal kurang']);
        $this->seeInStore('payments', ['order_id' => $order['id'], 'payment_status' => 'rejected']);
        $this->seeInStore('orders', ['id' => $order['id'], 'order_status' => 'pending_payment']);

        // Customer mengunggah ulang, admin menyetujui.
        (new OrderService())->submitPayment(model(OrderModel::class)->find($order['id']), $users->findByEmail(self::CUSTOMER)['id'], 'bukti-uji-2.png');
        $this->actingAs('admin@shopease.test')->post('admin/orders/' . $order['id'] . '/verify', ['decision' => 'approve']);
        $this->seeInStore('orders', ['id' => $order['id'], 'order_status' => 'processing']);
        $this->seeInStore('payments', ['order_id' => $order['id'], 'payment_status' => 'verified', 'verified_by' => $users->findByEmail('admin@shopease.test')['id']]);

        // Operator mengisi resi.
        $this->actingAs('operator@shopease.test')->post('admin/orders/' . $order['id'] . '/ship', ['courier' => 'JNE', 'tracking_number' => 'JNE12345678']);
        $this->seeInStore('orders', ['id' => $order['id'], 'order_status' => 'shipped']);
        $this->seeInStore('shipments', ['order_id' => $order['id'], 'tracking_number' => 'JNE12345678', 'shipment_status' => 'shipped']);

        // Customer mengonfirmasi pesanan diterima.
        $this->actingAs(self::CUSTOMER)->post('orders/' . $order['order_number'] . '/complete');
        $this->seeInStore('orders', ['id' => $order['id'], 'order_status' => 'completed']);
        $this->seeInStore('shipments', ['order_id' => $order['id'], 'shipment_status' => 'delivered']);

        // Setiap perubahan status tercatat (BR-012).
        $this->assertCount(7, model(\App\Models\OrderLogModel::class)->where(['order_id' => $order['id']]));
    }

    public function testInvalidStatusTransitionIsRejected(): void
    {
        $order = model(OrderModel::class)->firstWhere(['order_status' => 'pending_payment']);

        // Pesanan belum dibayar tidak boleh langsung dikirim.
        $this->actingAs('operator@shopease.test')->post('admin/orders/' . $order['id'] . '/ship', ['courier' => 'JNE', 'tracking_number' => 'JNE12345678'])
            ->assertSessionHas('error');
        $this->seeInStore('orders', ['id' => $order['id'], 'order_status' => 'pending_payment']);
    }

    public function testCancellingOrderRestoresStock(): void
    {
        $product = $this->productBySlug('kaos-katun-basic');
        $this->addToCart($product['id'], 4);
        $this->checkout();
        $order = $this->latestOrder();
        $this->assertSame($product['stock'] - 4, (int) $this->productBySlug('kaos-katun-basic')['stock']);

        $this->actingAs(self::CUSTOMER)->post('orders/' . $order['order_number'] . '/cancel');

        $this->seeInStore('orders', ['id' => $order['id'], 'order_status' => 'cancelled']);
        $this->assertSame((int) $product['stock'], (int) $this->productBySlug('kaos-katun-basic')['stock']);
    }

    private function latestOrder(): array
    {
        $orders = model(OrderModel::class)->all();

        return end($orders);
    }

    private function addToCart(int $productId, int $quantity): void
    {
        $this->actingAs(self::CUSTOMER)->post('cart', ['product_id' => $productId, 'quantity' => $quantity])->assertSessionHas('success');
    }

    /**
     * @param string|null $sessionToken token yang tersimpan di session (null = sudah terpakai)
     */
    private function checkout(string $token = 'token-uji', ?string $sessionToken = 'token-uji')
    {
        $userId  = model(UserModel::class)->findByEmail(self::CUSTOMER)['id'];
        $address = model(AddressModel::class)->forUser($userId)[0];

        return $this->actingAs(self::CUSTOMER, $sessionToken === null ? [] : ['checkout_token' => $sessionToken])
            ->post('checkout', ['address_id' => $address['id'], 'checkout_token' => $token]);
    }

    private function cartItem(int $productId): ?array
    {
        $userId = model(UserModel::class)->findByEmail(self::CUSTOMER)['id'];

        return model(CartItemModel::class)->firstWhere(['user_id' => $userId, 'product_id' => $productId]);
    }
}
