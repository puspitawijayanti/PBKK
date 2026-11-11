<?php

namespace Tests\Feature;

use App\Models\UserModel;
use Tests\Support\ShopTestCase;

/**
 * Registrasi, login, logout (AC-01, AC-02).
 *
 * @internal
 */
final class AuthTest extends ShopTestCase
{
    public function testCustomerCanRegisterAndPasswordIsHashed(): void
    {
        $result = $this->post('register', [
            'name'             => 'Customer Baru',
            'email'            => 'Baru@Example.test',
            'password'         => 'rahasia123',
            'password_confirm' => 'rahasia123',
        ]);

        $result->assertRedirectTo(url_to('home'));
        $result->assertSessionHas('user_role', 'customer');

        $user = model(UserModel::class)->findByEmail('baru@example.test');
        $this->assertNotNull($user);
        $this->assertSame('customer', $user['role']);
        $this->assertNotSame('rahasia123', $user['password_hash']);
        $this->assertTrue(password_verify('rahasia123', $user['password_hash']));
    }

    public function testRegisterRejectsDuplicateEmail(): void
    {
        $result = $this->post('register', [
            'name'             => 'Budi Kedua',
            'email'            => 'budi@example.test',
            'password'         => 'rahasia123',
            'password_confirm' => 'rahasia123',
        ]);

        $result->assertSessionHas('errors');
        $this->assertSame(1, model(UserModel::class)->count(['email' => 'budi@example.test']));
    }

    public function testLoginWithValidCredentials(): void
    {
        $result = $this->post('login', ['email' => 'budi@example.test', 'password' => 'customer123']);

        $result->assertRedirectTo(url_to('home'));
        $result->assertSessionHas('user_name', 'Budi Santoso');
    }

    public function testAdminIsRedirectedToDashboardAfterLogin(): void
    {
        $this->post('login', ['email' => 'admin@shopease.test', 'password' => 'admin123'])
            ->assertRedirectTo(url_to('admin.dashboard'));
    }

    public function testLoginErrorDoesNotRevealWhetherEmailExists(): void
    {
        $wrongPassword = $this->post('login', ['email' => 'budi@example.test', 'password' => 'salah']);
        $unknownEmail  = $this->post('login', ['email' => 'tidakada@example.test', 'password' => 'salah']);

        $wrongPassword->assertSessionHas('error', 'Email atau password salah.');
        $unknownEmail->assertSessionHas('error', 'Email atau password salah.');
        $wrongPassword->assertSessionMissing('user_id');
    }

    public function testInactiveAccountCannotLogin(): void
    {
        $andi = model(UserModel::class)->findByEmail('andi@example.test');
        model(UserModel::class)->update($andi['id'], ['is_active' => false]);

        $this->post('login', ['email' => 'andi@example.test', 'password' => 'customer123'])
            ->assertSessionMissing('user_id');
    }

    public function testLogoutEndsSession(): void
    {
        $result = $this->actingAs('budi@example.test')->post('logout');

        $result->assertRedirectTo(url_to('home'));
        $result->assertSessionMissing('user_id');
    }
}
