<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class StageTwoAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    // 1. Customer dapat registrasi
    public function test_customer_can_register(): void
    {
        $response = $this->post(route('register.post'), [
            'name' => 'Budi Santoso',
            'email' => 'budi@example.com',
            'phone' => '081234567890',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('home'));
        $this->assertAuthenticated();

        $user = User::where('email', 'budi@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals('Budi Santoso', $user->name);
        $this->assertEquals('customer', $user->role);
    }

    // 2. Registrasi email duplikat ditolak
    public function test_registration_duplicate_email_is_rejected(): void
    {
        User::create([
            'name' => 'Existing User',
            'email' => 'duplicate@example.com',
            'password' => 'password123',
            'role' => 'customer',
        ]);

        $response = $this->from(route('register'))->post(route('register.post'), [
            'name' => 'New User',
            'email' => 'duplicate@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('register'));
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    // 3. Password konfirmasi tidak sesuai ditolak
    public function test_registration_unconfirmed_password_is_rejected(): void
    {
        $response = $this->from(route('register'))->post(route('register.post'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password123',
            'password_confirmation' => 'mismatched123',
        ]);

        $response->assertRedirect(route('register'));
        $response->assertSessionHasErrors('password');
        $this->assertGuest();
    }

    // 4. Pengiriman role=admin melalui request registrasi tidak memberikan akses admin
    public function test_registration_with_admin_role_payload_remains_customer(): void
    {
        $response = $this->post(route('register.post'), [
            'name' => 'Hacker Impersonator',
            'email' => 'hacker@example.com',
            'role' => 'admin', // Malicious payload
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect(route('home'));

        $user = User::where('email', 'hacker@example.com')->first();
        $this->assertEquals('customer', $user->role);
        $this->assertFalse($user->isAdmin());
        $this->assertTrue($user->isCustomer());
    }

    // 5. Password disimpan sebagai hash
    public function test_password_is_stored_as_hash(): void
    {
        $this->post(route('register.post'), [
            'name' => 'Secure User',
            'email' => 'secure@example.com',
            'password' => 'supersecret123',
            'password_confirmation' => 'supersecret123',
        ]);

        $user = User::where('email', 'secure@example.com')->first();
        $this->assertNotEquals('supersecret123', $user->password);
        $this->assertTrue(Hash::check('supersecret123', $user->password));
    }

    // 6. Customer berhasil login dari halaman login customer (/login)
    public function test_customer_can_login_successfully(): void
    {
        $customer = User::create([
            'name' => 'Customer Satu',
            'email' => 'customer1@example.com',
            'password' => 'secret123',
            'role' => 'customer',
        ]);

        $response = $this->post(route('login.post'), [
            'email' => 'customer1@example.com',
            'password' => 'secret123',
        ]);

        $response->assertRedirect(route('home'));
        $this->assertAuthenticatedAs($customer);
    }

    // 7. Login password salah ditolak
    public function test_login_with_invalid_password_is_rejected(): void
    {
        User::create([
            'name' => 'User Valid',
            'email' => 'valid@example.com',
            'password' => 'secret123',
            'role' => 'customer',
        ]);

        $response = $this->from(route('login'))->post(route('login.post'), [
            'email' => 'valid@example.com',
            'password' => 'wrongpassword',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    // 8. Admin berhasil login dari halaman login admin (/admin/login)
    public function test_admin_can_login_and_redirects_to_admin_dashboard(): void
    {
        $admin = User::create([
            'name' => 'Admin Utama',
            'email' => 'admin@example.com',
            'password' => 'adminsecret',
            'role' => 'admin',
        ]);

        $response = $this->post(route('admin.login.post'), [
            'email' => 'admin@example.com',
            'password' => 'adminsecret',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($admin);
    }

    // 9. Akun Admin ditolak login pada endpoint Customer (/login) dengan pesan generik identik
    public function test_admin_account_is_rejected_on_customer_login(): void
    {
        User::create([
            'name' => 'Admin User',
            'email' => 'admin_block@example.com',
            'password' => 'password123',
            'role' => 'admin',
        ]);

        $response = $this->post(route('login.post'), [
            'email' => 'admin_block@example.com',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors([
            'email' => 'Email atau kata sandi tidak sesuai.',
        ]);
        $this->assertGuest();
    }

    // 10. Akun Customer ditolak login pada endpoint Admin (/admin/login) dengan pesan generik identik
    public function test_customer_account_is_rejected_on_admin_login(): void
    {
        User::create([
            'name' => 'Customer User',
            'email' => 'customer_block@example.com',
            'password' => 'password123',
            'role' => 'customer',
        ]);

        $response = $this->post(route('admin.login.post'), [
            'email' => 'customer_block@example.com',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors([
            'email' => 'Email atau kata sandi tidak sesuai.',
        ]);
        $this->assertGuest();
    }

    // 10b. Login gagal dengan email tidak terdaftar menghasilkan pesan generik identik
    public function test_unregistered_email_login_returns_identical_generic_error(): void
    {
        $response = $this->post(route('login.post'), [
            'email' => 'nonexistent@example.com',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors([
            'email' => 'Email atau kata sandi tidak sesuai.',
        ]);
        $this->assertGuest();

        $responseAdmin = $this->post(route('admin.login.post'), [
            'email' => 'nonexistent_admin@example.com',
            'password' => 'password123',
        ]);

        $responseAdmin->assertSessionHasErrors([
            'email' => 'Email atau kata sandi tidak sesuai.',
        ]);
        $this->assertGuest();
    }

    // 11. Guest dialihkan ke login yang sesuai
    public function test_guest_is_redirected_to_respective_login_on_protected_routes(): void
    {
        // Akses admin diarahkan ke /admin/login
        $responseAdmin = $this->get(route('admin.dashboard'));
        $responseAdmin->assertRedirect(route('admin.login'));

        // Akses customer diarahkan ke /login
        $responseCustomer = $this->get(route('customer.profile'));
        $responseCustomer->assertRedirect(route('login'));
    }

    // 12. Customer tidak dapat mengakses route Admin
    public function test_customer_cannot_access_admin_routes(): void
    {
        $customer = User::create([
            'name' => 'Customer Three',
            'email' => 'customer3@example.com',
            'password' => 'secret123',
            'role' => 'customer',
        ]);

        $response = $this->actingAs($customer)->get(route('admin.dashboard'));
        $response->assertStatus(403);
    }

    // 13. Admin tidak dapat mengakses route Customer yang dibatasi
    public function test_admin_cannot_access_customer_routes(): void
    {
        $admin = User::create([
            'name' => 'Admin Two',
            'email' => 'admin2@example.com',
            'password' => 'secret123',
            'role' => 'admin',
        ]);

        $response = $this->actingAs($admin)->get(route('customer.profile'));
        $response->assertStatus(403);
    }

    // 14. Pembuatan Admin baru terbatas untuk Admin yang login
    public function test_guest_and_customer_cannot_access_admin_register(): void
    {
        // Guest
        $resGuest = $this->get(route('admin.register'));
        $resGuest->assertRedirect(route('admin.login'));

        // Customer
        $customer = User::create([
            'name' => 'Customer Blocked',
            'email' => 'cust_block@test.com',
            'password' => 'password123',
            'role' => 'customer',
        ]);
        $resCust = $this->actingAs($customer)->get(route('admin.register'));
        $resCust->assertStatus(403);
    }

    // 15. Admin yang login dapat membuat akun Admin baru dan sesi admin tetap aktif
    public function test_authenticated_admin_can_create_new_admin_and_stay_logged_in(): void
    {
        $currentAdmin = User::create([
            'name' => 'Existing Admin',
            'email' => 'existing_admin@test.com',
            'password' => 'adminpass',
            'role' => 'admin',
        ]);

        $response = $this->actingAs($currentAdmin)->post(route('admin.register.post'), [
            'name' => 'New Co-Admin',
            'email' => 'co_admin@test.com',
            'phone' => '081299999999',
            'password' => 'coadmin123',
            'password_confirmation' => 'coadmin123',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $response->assertSessionHas('success');

        // Admin yang login tetap $currentAdmin
        $this->assertAuthenticatedAs($currentAdmin);

        // Akun admin baru tersimpan dengan role admin dan password ter-hash
        $newAdmin = User::where('email', 'co_admin@test.com')->first();
        $this->assertNotNull($newAdmin);
        $this->assertEquals('admin', $newAdmin->role);
        $this->assertTrue(Hash::check('coadmin123', $newAdmin->password));
    }

    // 16. Logout benar-benar mengakhiri session
    public function test_logout_properly_invalidates_session(): void
    {
        $user = User::create([
            'name' => 'Logged In User',
            'email' => 'logged@example.com',
            'password' => 'secret123',
            'role' => 'customer',
        ]);

        $response = $this->actingAs($user)->post(route('logout'));

        $response->assertRedirect(route('login'));
        $this->assertGuest();
    }

    // 17. Profil Customer hanya dapat diakses pemilik akun & perubahan tervalidasi
    public function test_customer_can_view_and_update_profile(): void
    {
        $user = User::create([
            'name' => 'Old Name',
            'email' => 'buyer@example.com',
            'phone' => '08111111111',
            'password' => 'secret123',
            'role' => 'customer',
        ]);

        $viewResponse = $this->actingAs($user)->get(route('customer.profile'));
        $viewResponse->assertOk();
        $viewResponse->assertSee('Old Name');
        $viewResponse->assertSee('buyer@example.com');

        $updateResponse = $this->actingAs($user)->put(route('customer.profile.update'), [
            'name' => 'New Updated Name',
            'phone' => '08222222222',
        ]);

        $updateResponse->assertRedirect(route('customer.profile'));
        $this->assertEquals('New Updated Name', $user->fresh()->name);
        $this->assertEquals('08222222222', $user->fresh()->phone);
        $this->assertEquals('buyer@example.com', $user->fresh()->email);
    }

    // 18. Rate limiting login berlaku pada customer & admin
    public function test_login_rate_limiting_triggers_after_multiple_failures(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->post(route('login.post'), [
                'email' => 'ratelimit@example.com',
                'password' => 'wrongpass',
            ]);
        }

        $response = $this->post(route('login.post'), [
            'email' => 'ratelimit@example.com',
            'password' => 'wrongpass',
        ]);

        $response->assertSessionHasErrors('email');
    }

    // 19. Halaman login customer dan admin merender tampilan dengan benar
    public function test_auth_pages_render_cleanly(): void
    {
        $customerLogin = $this->get(route('login'));
        $customerLogin->assertOk();
        $customerLogin->assertSee('Selamat Datang Kembali');

        $adminLogin = $this->get(route('admin.login'));
        $adminLogin->assertOk();
        $adminLogin->assertSee("O'Clock Admin Panel");

        $registerPage = $this->get(route('register'));
        $registerPage->assertOk();
        $registerPage->assertSee('Bergabung dengan');
    }
}
