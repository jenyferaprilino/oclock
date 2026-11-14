<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\City;
use App\Models\Product;
use App\Models\Province;
use App\Models\ShippingRate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StageThreeAdminTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected User $customer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'Admin Utama',
            'email' => 'admin@oclock.test',
            'password' => 'password123',
            'role' => 'admin',
        ]);

        $this->customer = User::create([
            'name' => 'Customer Biasa',
            'email' => 'customer@oclock.test',
            'password' => 'password123',
            'role' => 'customer',
        ]);
    }

    // 1. Admin dapat mengakses dashboard
    public function test_admin_can_access_dashboard(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));
        $response->assertOk();
        $response->assertSee('Ringkasan Toko &amp; Operasional', false);
    }

    // 2. Customer ditolak dari halaman Admin (HTTP 403)
    public function test_customer_is_forbidden_from_admin_dashboard(): void
    {
        $response = $this->actingAs($this->customer)->get(route('admin.dashboard'));
        $response->assertStatus(403);
    }

    // 3. Guest diarahkan ke login admin (/admin/login)
    public function test_guest_is_redirected_to_login_from_admin(): void
    {
        $response = $this->get(route('admin.dashboard'));
        $response->assertRedirect(route('admin.login'));
    }

    // 4. Statistik dashboard menggunakan data aktual dari database
    public function test_dashboard_statistics_reflect_actual_database_data(): void
    {
        $category = Category::create(['name' => 'Dress Watch', 'slug' => 'dress-watch']);
        Product::create([
            'category_id' => $category->id,
            'name' => 'Classic Gold',
            'slug' => 'classic-gold',
            'sku' => 'OC-DW-001',
            'brand' => 'O\'Clock',
            'description' => 'Test desc',
            'price' => 500000,
            'stock' => 10,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->get(route('admin.dashboard'));
        $response->assertOk();
        $response->assertViewHas('stats', function ($stats) {
            return $stats['total_products'] === 1 &&
                   $stats['total_categories'] === 1 &&
                   $stats['total_customers'] === 1 &&
                   $stats['total_orders'] === 0;
        });
    }

    // 5. Admin dapat menambah kategori
    public function test_admin_can_create_category(): void
    {
        $response = $this->actingAs($this->admin)->post(route('admin.categories.store'), [
            'name' => 'Heritage Vintage',
        ]);

        $response->assertRedirect(route('admin.categories.index'));
        $this->assertDatabaseHas('categories', [
            'name' => 'Heritage Vintage',
            'slug' => 'heritage-vintage',
        ]);
    }

    // 6. Admin dapat mengubah kategori
    public function test_admin_can_update_category(): void
    {
        $category = Category::create(['name' => 'Pilot Old', 'slug' => 'pilot-old']);

        $response = $this->actingAs($this->admin)->put(route('admin.categories.update', $category), [
            'name' => 'Pilot Modernized',
            'slug' => 'pilot-modernized',
        ]);

        $response->assertRedirect(route('admin.categories.index'));
        $this->assertEquals('Pilot Modernized', $category->fresh()->name);
        $this->assertEquals('pilot-modernized', $category->fresh()->slug);
    }

    // 7. Penghapusan kategori yang memiliki produk ditolak
    public function test_deleting_category_with_products_is_rejected(): void
    {
        $category = Category::create(['name' => 'Active Category', 'slug' => 'active-category']);
        Product::create([
            'category_id' => $category->id,
            'name' => 'Linked Watch',
            'slug' => 'linked-watch',
            'sku' => 'LNK-001',
            'brand' => 'O\'Clock',
            'description' => 'Desc',
            'price' => 300000,
            'stock' => 5,
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.categories.destroy', $category));

        $response->assertRedirect(route('admin.categories.index'));
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('categories', ['id' => $category->id]);
    }

    // 8. Admin dapat menambah produk
    public function test_admin_can_create_product(): void
    {
        $category = Category::create(['name' => 'Luxury', 'slug' => 'luxury']);

        $response = $this->actingAs($this->admin)->post(route('admin.products.store'), [
            'category_id' => $category->id,
            'name' => 'Vanguard Royal Oak',
            'sku' => 'VAN-RO-001',
            'brand' => 'Vanguard',
            'description' => 'High precision automatic luxury watch.',
            'price' => 1500000,
            'stock' => 8,
            'watch_type' => 'Automatic',
            'case_diameter' => '41mm',
            'is_active' => 1,
        ]);

        $response->assertRedirect(route('admin.products.index'));
        $this->assertDatabaseHas('products', [
            'sku' => 'VAN-RO-001',
            'name' => 'Vanguard Royal Oak',
            'price' => 1500000,
            'stock' => 8,
        ]);
    }

    // 9. SKU duplikat ditolak
    public function test_duplicate_sku_is_rejected(): void
    {
        $category = Category::create(['name' => 'Casual', 'slug' => 'casual']);
        Product::create([
            'category_id' => $category->id,
            'name' => 'Watch 1',
            'slug' => 'watch-1',
            'sku' => 'DUP-SKU-001',
            'brand' => 'Brand',
            'description' => 'Desc',
            'price' => 100000,
            'stock' => 1,
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.products.store'), [
            'category_id' => $category->id,
            'name' => 'Watch 2',
            'sku' => 'DUP-SKU-001',
            'brand' => 'Brand',
            'description' => 'Desc',
            'price' => 200000,
            'stock' => 2,
        ]);

        $response->assertSessionHasErrors('sku');
    }

    // 10 & 11. Harga <= 0 atau stok negatif ditolak
    public function test_negative_or_zero_price_and_negative_stock_are_rejected(): void
    {
        $category = Category::create(['name' => 'Casual', 'slug' => 'casual']);

        // Zero price
        $resZero = $this->actingAs($this->admin)->post(route('admin.products.store'), [
            'category_id' => $category->id,
            'name' => 'Free Watch',
            'sku' => 'FREE-001',
            'brand' => 'Brand',
            'description' => 'Desc',
            'price' => 0,
            'stock' => 5,
        ]);
        $resZero->assertSessionHasErrors('price');

        // Negative stock
        $resNeg = $this->actingAs($this->admin)->post(route('admin.products.store'), [
            'category_id' => $category->id,
            'name' => 'Ghost Watch',
            'sku' => 'GHOST-001',
            'brand' => 'Brand',
            'description' => 'Desc',
            'price' => 100000,
            'stock' => -5,
        ]);
        $resNeg->assertSessionHasErrors('stock');
    }

    // 12. Upload gambar valid berhasil
    public function test_valid_image_upload_succeeds(): void
    {
        Storage::fake('public');
        $category = Category::create(['name' => 'Casual', 'slug' => 'casual']);

        $file = UploadedFile::fake()->image('watch.jpg', 600, 600)->size(500);

        $response = $this->actingAs($this->admin)->post(route('admin.products.store'), [
            'category_id' => $category->id,
            'name' => 'Photo Watch',
            'sku' => 'PHOTO-001',
            'brand' => 'Brand',
            'description' => 'Desc',
            'price' => 500000,
            'stock' => 5,
            'image' => $file,
        ]);

        $response->assertRedirect(route('admin.products.index'));

        $product = Product::where('sku', 'PHOTO-001')->first();
        $this->assertNotNull($product->image_path);
        Storage::disk('public')->assertExists($product->image_path);
    }

    // 13. Upload file tidak valid (bukan gambar / terlalu besar) ditolak
    public function test_invalid_image_upload_is_rejected(): void
    {
        Storage::fake('public');
        $category = Category::create(['name' => 'Casual', 'slug' => 'casual']);

        $pdfFile = UploadedFile::fake()->create('document.pdf', 100, 'application/pdf');

        $response = $this->actingAs($this->admin)->post(route('admin.products.store'), [
            'category_id' => $category->id,
            'name' => 'Fake Image Watch',
            'sku' => 'FAKE-001',
            'brand' => 'Brand',
            'description' => 'Desc',
            'price' => 500000,
            'stock' => 5,
            'image' => $pdfFile,
        ]);

        $response->assertSessionHasErrors('image');
    }

    // 14. Edit produk berjalan
    public function test_admin_can_update_product(): void
    {
        $category = Category::create(['name' => 'Sport', 'slug' => 'sport']);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Old Watch Name',
            'slug' => 'old-watch-name',
            'sku' => 'OLD-001',
            'brand' => 'Apex',
            'description' => 'Old desc',
            'price' => 400000,
            'stock' => 10,
        ]);

        $response = $this->actingAs($this->admin)->put(route('admin.products.update', $product), [
            'category_id' => $category->id,
            'name' => 'New Watch Name',
            'sku' => 'OLD-001',
            'brand' => 'Apex',
            'description' => 'Updated desc',
            'price' => 450000,
            'stock' => 12,
            'expected_stock' => 10,
        ]);

        $response->assertRedirect(route('admin.products.index'));
        $this->assertEquals('New Watch Name', $product->fresh()->name);
        $this->assertEquals(450000, $product->fresh()->price);
        $this->assertEquals(12, $product->fresh()->stock);
    }

    // 15. Soft delete produk berfungsi
    public function test_product_soft_delete_works(): void
    {
        $category = Category::create(['name' => 'Diver', 'slug' => 'diver']);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Delete Me Watch',
            'slug' => 'delete-me-watch',
            'sku' => 'DEL-001',
            'brand' => 'Nautilus',
            'description' => 'Desc',
            'price' => 900000,
            'stock' => 3,
        ]);

        $response = $this->actingAs($this->admin)->delete(route('admin.products.destroy', $product));

        $response->assertRedirect(route('admin.products.index'));
        $this->assertSoftDeleted('products', ['id' => $product->id]);
    }

    // 16. Perubahan stok berjalan benar
    public function test_stock_update_endpoint_works(): void
    {
        $category = Category::create(['name' => 'Formal', 'slug' => 'formal']);
        $product = Product::create([
            'category_id' => $category->id,
            'name' => 'Stock Watch',
            'slug' => 'stock-watch',
            'sku' => 'STK-001',
            'brand' => 'Vanguard',
            'description' => 'Desc',
            'price' => 700000,
            'stock' => 10,
        ]);

        $response = $this->actingAs($this->admin)->patch(route('admin.products.stock', $product), [
            'stock' => 25,
            'expected_stock' => 10,
        ]);

        $response->assertSessionHas('success');
        $this->assertEquals(25, $product->fresh()->stock);
    }

    // 17. Tarif ongkir dapat ditambah dan diubah
    public function test_shipping_rate_can_be_created_and_updated(): void
    {
        $province = Province::create(['code' => '17', 'name' => 'Bengkulu']);
        $city = City::create([
            'province_id' => $province->id,
            'code' => '1771',
            'name' => 'Bengkulu',
            'type' => 'city',
        ]);

        // Create rate
        $resStore = $this->actingAs($this->admin)->post(route('admin.shipping-rates.store'), [
            'city_id' => $city->id,
            'price' => 10000,
            'is_active' => 1,
        ]);
        $resStore->assertRedirect(route('admin.shipping-rates.index'));
        $this->assertDatabaseHas('shipping_rates', [
            'city_id' => $city->id,
            'price' => 10000,
        ]);

        // Update rate
        $rate = ShippingRate::where('city_id', $city->id)->first();
        $resUpdate = $this->actingAs($this->admin)->put(route('admin.shipping-rates.update', $rate), [
            'price' => 12000,
            'is_active' => 1,
        ]);
        $resUpdate->assertRedirect(route('admin.shipping-rates.index'));
        $this->assertEquals(12000, $rate->fresh()->price);
    }

    // 18. Tarif ongkir duplikat untuk kota sama ditolak
    public function test_duplicate_shipping_rate_for_same_city_is_rejected(): void
    {
        $province = Province::create(['code' => '31', 'name' => 'DKI Jakarta']);
        $city = City::create([
            'province_id' => $province->id,
            'code' => '3171',
            'name' => 'Jakarta Pusat',
            'type' => 'city',
        ]);

        ShippingRate::create([
            'city_id' => $city->id,
            'price' => 35000,
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.shipping-rates.store'), [
            'city_id' => $city->id,
            'price' => 40000,
        ]);

        $response->assertSessionHasErrors('city_id');
    }

    // 19. Tarif tidak valid (negatif) ditolak
    public function test_negative_shipping_rate_is_rejected(): void
    {
        $province = Province::create(['code' => '32', 'name' => 'Jawa Barat']);
        $city = City::create([
            'province_id' => $province->id,
            'code' => '3273',
            'name' => 'Bandung',
            'type' => 'city',
        ]);

        $response = $this->actingAs($this->admin)->post(route('admin.shipping-rates.store'), [
            'city_id' => $city->id,
            'price' => -10000,
        ]);

        $response->assertSessionHasErrors('price');
    }

    // 20 & 21. Admin dapat melihat daftar Customer & password tidak ditampilkan
    public function test_admin_can_view_customers_without_exposing_password(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.customers.index'));
        $response->assertOk();
        $response->assertSee('Customer Biasa');
        $response->assertSee('customer@oclock.test');
        // Pastikan hash password tidak tampil dalam HTML
        $response->assertDontSee($this->customer->password);
    }

    // 22. Halaman daftar pesanan menangani kondisi kosong secara benar
    public function test_admin_orders_page_handles_empty_state_cleanly(): void
    {
        $response = $this->actingAs($this->admin)->get(route('admin.orders.index'));
        $response->assertOk();
        $response->assertSee('Belum ada transaksi pesanan yang masuk.');
    }

    // 23. Customer tidak dapat mengakses endpoint CRUD Admin
    public function test_customer_cannot_access_crud_admin_endpoints(): void
    {
        $resCat = $this->actingAs($this->customer)->post(route('admin.categories.store'), ['name' => 'Hacker']);
        $resCat->assertStatus(403);

        $resProd = $this->actingAs($this->customer)->post(route('admin.products.store'), ['name' => 'Hacker']);
        $resProd->assertStatus(403);

        $resShip = $this->actingAs($this->customer)->get(route('admin.shipping-rates.index'));
        $resShip->assertStatus(403);
    }
}
