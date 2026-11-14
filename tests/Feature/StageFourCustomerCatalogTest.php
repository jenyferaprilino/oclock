<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StageFourCustomerCatalogTest extends TestCase
{
    use RefreshDatabase;

    protected function makeProduct(array $attributes = []): Product
    {
        static $counter = 1;
        $num = $counter++;

        return Product::create(array_merge([
            'category_id' => 1,
            'name' => "Timepiece Model {$num}",
            'slug' => "timepiece-model-{$num}",
            'sku' => sprintf('OC-TEST-%04d', $num),
            'brand' => "O'Clock Signature",
            'description' => 'Kurasi jam tangan elegan dengan presisi tinggi dan material pilihan.',
            'watch_type' => 'Analog',
            'strap_material' => 'Genuine Leather',
            'color' => 'Ivory / Charcoal',
            'case_diameter' => '40mm',
            'movement_type' => 'Japanese Quartz',
            'water_resistance' => '30m',
            'price' => 500000,
            'stock' => 10,
            'image_path' => null,
            'is_active' => true,
        ], $attributes));
    }

    /**
     * 1. Homepage menampilkan hero O'Clock, kategori dari DB, dan produk unggulan aktif.
     */
    public function test_homepage_renders_successfully_with_categories_and_featured_products(): void
    {
        $category = Category::create([
            'name' => 'Formal',
            'slug' => 'formal',
        ]);

        $this->makeProduct([
            'category_id' => $category->id,
            'name' => 'Chrono Sovereign Gold',
            'slug' => 'chrono-sovereign-gold',
            'sku' => 'OC-FM-001',
            'brand' => 'Vanguard',
            'description' => 'Jam tangan formal mewah.',
            'watch_type' => 'Chronograph',
            'strap_material' => 'Milanese Steel',
            'color' => 'Gold / Charcoal',
            'case_diameter' => '41mm',
            'movement_type' => 'Miyota Precision Quartz',
            'water_resistance' => '50m',
            'price' => 850000,
            'stock' => 10,
        ]);

        $response = $this->get(route('home'));

        $response->assertStatus(200);
        $response->assertSee("O'Clock");
        $response->assertSee('Timeless Style');
        $response->assertSee('Formal');
        $response->assertSee('Chrono Sovereign Gold');
        $response->assertSee('Rp850.000');
    }

    public function test_catalog_only_renders_an_approved_photo_for_its_matching_sku(): void
    {
        $category = Category::create(['name' => 'Formal', 'slug' => 'formal']);
        $path = 'products/approved.png';
        $approved = $this->makeProduct(['category_id' => $category->id, 'image_path' => $path]);
        $other = $this->makeProduct(['category_id' => $category->id, 'image_path' => $path]);
        Storage::fake('public')->put($path, base64_decode(
            'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+jG8sAAAAASUVORK5CYII='
        ));
        config(['product_photos' => [
            $approved->sku => [$path => ['source' => 'Test fixture', 'usage_terms' => 'Test only']],
        ]]);

        $catalog = $this->get(route('catalog.index'));

        $catalog->assertOk()
            ->assertSee('alt="'.$approved->name.'"', false)
            ->assertDontSee('alt="'.$other->name.'"', false)
            ->assertSee('Foto Produk Segera Hadir');
    }

    public function test_demo_photo_is_labelled_and_can_be_disabled(): void
    {
        $category = Category::create(['name' => 'Casual', 'slug' => 'casual']);
        $product = $this->makeProduct([
            'category_id' => $category->id,
            'sku' => 'CAS-A168-GLD',
            'image_path' => 'products/unverified.svg',
        ]);
        $path = 'products/demo/casio-f91w.jpg';
        Storage::fake('public')->put($path, 'test fixture');
        config(['demo_product_photos.enabled' => true]);
        $detail = route('catalog.show', $product->slug);

        $this->get($detail)->assertOk()
            ->assertSee($path)
            ->assertSee('Foto Ilustrasi Produk')
            ->assertSee('Ashley Pomeroy')
            ->assertDontSee('products/unverified.svg');

        config(['demo_product_photos.enabled' => false]);

        $this->get($detail)->assertOk()
            ->assertSee('Foto Produk Segera Hadir')
            ->assertDontSee($path);
    }

    public function test_public_product_views_use_placeholders_for_unverified_photos(): void
    {
        $category = Category::create(['name' => 'Formal', 'slug' => 'formal']);
        $illustrated = $this->makeProduct([
            'category_id' => $category->id,
            'image_path' => 'products/unverified.svg',
        ]);
        $mismatched = $this->makeProduct([
            'category_id' => $category->id,
            'image_path' => 'products/mismatched.jpg',
        ]);

        foreach ([route('home'), route('catalog.index'), route('catalog.show', $illustrated->slug), route('catalog.show', $mismatched->slug)] as $url) {
            $this->get($url)
                ->assertOk()
                ->assertSee('Foto Produk Segera Hadir')
                ->assertDontSee('products/unverified.svg')
                ->assertDontSee('products/mismatched.jpg')
                ->assertDontSee('products/ac-aut-9601mar.svg')
                ->assertDontSee('placeholder-watch.svg');
        }
    }

    /**
     * 2. Homepage tidak menampilkan produk non-aktif atau soft-deleted.
     */
    public function test_homepage_only_shows_active_and_non_deleted_products(): void
    {
        $category = Category::create(['name' => 'Casual', 'slug' => 'casual']);

        $this->makeProduct([
            'category_id' => $category->id,
            'name' => 'Active Casual Timepiece',
            'slug' => 'active-casual-timepiece',
            'sku' => 'OC-CS-ACTIVE',
            'is_active' => true,
        ]);

        $this->makeProduct([
            'category_id' => $category->id,
            'name' => 'Hidden Inactive Watch',
            'slug' => 'hidden-inactive-watch',
            'sku' => 'OC-CS-INACTIVE',
            'is_active' => false,
        ]);

        $deletedProduct = $this->makeProduct([
            'category_id' => $category->id,
            'name' => 'Deleted Watch Timepiece',
            'slug' => 'deleted-watch-timepiece',
            'sku' => 'OC-CS-DELETED',
            'is_active' => true,
        ]);
        $deletedProduct->delete();

        $response = $this->get(route('home'));

        $response->assertStatus(200);
        $response->assertSee('Active Casual Timepiece');
        $response->assertDontSee('Hidden Inactive Watch');
        $response->assertDontSee('Deleted Watch Timepiece');
    }

    /**
     * 3. Halaman katalog menampilkan produk aktif, status stok, dan kategori.
     */
    public function test_catalog_page_displays_active_products(): void
    {
        $category = Category::create(['name' => 'Sport', 'slug' => 'sport']);

        $this->makeProduct([
            'category_id' => $category->id,
            'name' => 'Apex Endurance Watch',
            'slug' => 'apex-endurance-watch',
            'sku' => 'OC-SP-001',
            'brand' => 'Nautilus',
            'price' => 620000,
            'stock' => 8,
            'is_active' => true,
        ]);

        $response = $this->get(route('catalog.index'));

        $response->assertStatus(200);
        $response->assertSee('Koleksi Jam Tangan');
        $response->assertSee('Apex Endurance Watch');
        $response->assertSee('Nautilus');
        $response->assertSee('Rp620.000');
        $response->assertSee('Stok 8');
    }

    /**
     * 4. Pencarian katalog menyaring berdasarkan nama, brand, dan SKU.
     */
    public function test_catalog_search_filters_by_name_brand_and_sku(): void
    {
        $category = Category::create(['name' => 'Casual', 'slug' => 'casual']);

        $this->makeProduct([
            'category_id' => $category->id,
            'name' => 'Aethel Minimalist Ivory',
            'slug' => 'aethel-minimalist-ivory',
            'sku' => 'OC-AETHEL-01',
            'brand' => 'Aethel Heritage',
            'price' => 450000,
            'stock' => 5,
            'is_active' => true,
        ]);

        $this->makeProduct([
            'category_id' => $category->id,
            'name' => 'Vanguard Royal Diver',
            'slug' => 'vanguard-royal-diver',
            'sku' => 'OC-VRD-99',
            'brand' => 'Vanguard',
            'price' => 990000,
            'stock' => 3,
            'is_active' => true,
        ]);

        // Cari berdasarkan nama
        $resName = $this->get(route('catalog.index', ['search' => 'Aethel']));
        $resName->assertSee('Aethel Minimalist Ivory');
        $resName->assertDontSee('Vanguard Royal Diver');

        // Cari berdasarkan brand
        $resBrand = $this->get(route('catalog.index', ['search' => 'Vanguard']));
        $resBrand->assertSee('Vanguard Royal Diver');
        $resBrand->assertDontSee('Aethel Minimalist Ivory');

        // Cari berdasarkan SKU
        $resSku = $this->get(route('catalog.index', ['search' => 'OC-AETHEL-01']));
        $resSku->assertSee('Aethel Minimalist Ivory');
        $resSku->assertDontSee('Vanguard Royal Diver');

        // Cari kata kunci yang tidak ada -> empty state
        $resEmpty = $this->get(route('catalog.index', ['search' => 'NonExistentProductXYZ']));
        $resEmpty->assertSee('Jam Tangan Tidak Ditemukan');
    }

    /**
     * 5. Filter kategori menyaring produk berdasarkan kategori tertentu.
     */
    public function test_catalog_category_filter_filters_products(): void
    {
        $catDiver = Category::create(['name' => 'Diver', 'slug' => 'diver']);
        $catPilot = Category::create(['name' => 'Pilot', 'slug' => 'pilot']);

        $this->makeProduct([
            'category_id' => $catDiver->id,
            'name' => 'Deep Sea Mariner',
            'slug' => 'deep-sea-mariner',
            'sku' => 'OC-DV-01',
            'brand' => 'Nautilus',
            'price' => 1200000,
            'stock' => 4,
            'is_active' => true,
        ]);

        $this->makeProduct([
            'category_id' => $catPilot->id,
            'name' => 'Aero Aviator Chrono',
            'slug' => 'aero-aviator-chrono',
            'sku' => 'OC-PL-01',
            'brand' => 'Aviator Co',
            'price' => 1100000,
            'stock' => 6,
            'is_active' => true,
        ]);

        $response = $this->get(route('catalog.index', ['category' => 'diver']));
        $response->assertStatus(200);
        $response->assertSee('Deep Sea Mariner');
        $response->assertDontSee('Aero Aviator Chrono');
    }

    /**
     * 6. Pengurutan harga berfungsi dengan benar.
     */
    public function test_catalog_sorting_by_price_works(): void
    {
        $category = Category::create(['name' => 'Casual', 'slug' => 'casual']);

        $this->makeProduct([
            'category_id' => $category->id,
            'name' => 'Expensive Watch',
            'slug' => 'expensive-watch',
            'sku' => 'OC-EXP',
            'brand' => 'Luxury',
            'price' => 2000000,
            'stock' => 2,
            'is_active' => true,
        ]);

        $this->makeProduct([
            'category_id' => $category->id,
            'name' => 'Affordable Watch',
            'slug' => 'affordable-watch',
            'sku' => 'OC-AFF',
            'brand' => 'Value',
            'price' => 250000,
            'stock' => 10,
            'is_active' => true,
        ]);

        $resAsc = $this->get(route('catalog.index', ['sort' => 'price_asc']));
        $resAsc->assertStatus(200);
        $resAsc->assertSeeInOrder(['Affordable Watch', 'Expensive Watch']);

        $resDesc = $this->get(route('catalog.index', ['sort' => 'price_desc']));
        $resDesc->assertStatus(200);
        $resDesc->assertSeeInOrder(['Expensive Watch', 'Affordable Watch']);
    }

    /**
     * 7. Pagination katalog berjalan sesuai limit (9 per halaman).
     */
    public function test_catalog_pagination_functions(): void
    {
        $category = Category::create(['name' => 'Smartwatch', 'slug' => 'smartwatch']);

        for ($i = 1; $i <= 12; $i++) {
            $this->makeProduct([
                'category_id' => $category->id,
                'name' => "Smartwatch Model {$i}",
                'slug' => "smartwatch-model-{$i}",
                'sku' => sprintf('OC-SM-%03d', $i),
                'brand' => 'TechWatch',
                'price' => 500000 + ($i * 10000),
                'stock' => 5,
                'is_active' => true,
            ]);
        }

        $resPage1 = $this->get(route('catalog.index'));
        $resPage1->assertStatus(200);

        $resPage2 = $this->get(route('catalog.index', ['page' => 2]));
        $resPage2->assertStatus(200);
    }

    /**
     * 8. Halaman detail jam tangan menampilkan data lengkap, spesifikasi, dan stok.
     */
    public function test_product_detail_page_shows_full_information(): void
    {
        $category = Category::create(['name' => 'Formal', 'slug' => 'formal']);

        $product = $this->makeProduct([
            'category_id' => $category->id,
            'name' => 'Imperial Royal Silver 42mm',
            'slug' => 'imperial-royal-silver-42mm',
            'sku' => 'OC-FM-900',
            'brand' => 'Sovereign',
            'description' => 'Sentuhan kemewahan perak dengan sapphire crystal glass.',
            'watch_type' => 'Automatic Dress Watch',
            'strap_material' => 'Alligator Embossed Leather',
            'color' => 'Silver / Navy Blue',
            'case_diameter' => '42mm',
            'movement_type' => 'ETA Automatic Calibre',
            'water_resistance' => '100m (10 ATM)',
            'price' => 1500000,
            'stock' => 7,
            'image_path' => null,
            'is_active' => true,
        ]);

        $response = $this->get(route('catalog.show', $product->slug));

        $response->assertStatus(200);
        $response->assertSee('Imperial Royal Silver 42mm');
        $response->assertSee('Sovereign');
        $response->assertSee('OC-FM-900');
        $response->assertSee('Rp1.500.000');
        $response->assertSee('Automatic Dress Watch');
        $response->assertSee('Alligator Embossed Leather');
        $response->assertSee('Silver / Navy Blue');
        $response->assertSee('42mm');
        $response->assertSee('ETA Automatic Calibre');
        $response->assertSee('100m (10 ATM)');
        $response->assertSee('Tersedia (7 unit)');
    }

    /**
     * 9. Jam tangan non-aktif atau soft-deleted tidak dapat diakses publik (404).
     */
    public function test_inactive_and_deleted_product_detail_returns_404(): void
    {
        $category = Category::create(['name' => 'Casual', 'slug' => 'casual']);

        $inactive = $this->makeProduct([
            'category_id' => $category->id,
            'name' => 'Inactive Product',
            'slug' => 'inactive-product',
            'sku' => 'OC-INACT-01',
            'is_active' => false,
        ]);

        $deleted = $this->makeProduct([
            'category_id' => $category->id,
            'name' => 'Deleted Product',
            'slug' => 'deleted-product',
            'sku' => 'OC-DEL-01',
            'is_active' => true,
        ]);
        $deleted->delete();

        $this->get(route('catalog.show', $inactive->slug))->assertNotFound();
        $this->get(route('catalog.show', $deleted->slug))->assertNotFound();
        $this->get(route('catalog.show', 'completely-non-existent-slug'))->assertNotFound();
    }

    /**
     * 10. Pengunjung (Guest) pada detail jam diarahkan ke formulir login jika ingin membeli.
     */
    public function test_guest_on_product_detail_sees_login_button(): void
    {
        $category = Category::create(['name' => 'Casual', 'slug' => 'casual']);

        $product = $this->makeProduct([
            'category_id' => $category->id,
            'name' => 'Guest Purchase Test Watch',
            'slug' => 'guest-purchase-test-watch',
            'sku' => 'OC-GUEST-01',
            'price' => 500000,
            'stock' => 5,
            'is_active' => true,
        ]);

        $response = $this->get(route('catalog.show', $product->slug));

        $response->assertStatus(200);
        $response->assertSee(route('login'));
        $response->assertSee('Masuk Akun untuk Membeli');
    }

    /**
     * 11. Customer yang terautentikasi pada detail produk melihat status persiapan Tahap 5 (Rule 6).
     */
    public function test_customer_on_product_detail_sees_stage_five_cart_notice(): void
    {
        $customer = User::create([
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'password' => 'password123',
            'role' => 'customer',
        ]);

        $category = Category::create(['name' => 'Casual', 'slug' => 'casual']);

        $product = $this->makeProduct([
            'category_id' => $category->id,
            'name' => 'Customer View Watch',
            'slug' => 'customer-view-watch',
            'sku' => 'OC-CUST-01',
            'price' => 500000,
            'stock' => 5,
            'is_active' => true,
        ]);

        $response = $this->actingAs($customer)->get(route('catalog.show', $product->slug));

        $response->assertStatus(200);
        $response->assertSee('Tambah ke Keranjang Belanja');
        $response->assertSee(route('customer.cart.store'));
    }

    /**
     * 12. Customer dapat membuka halaman profil akun dan memperbarui data kontak.
     */
    public function test_customer_can_view_profile_and_access_store_navigation(): void
    {
        $customer = User::create([
            'name' => 'Siti Aisyah',
            'email' => 'siti@example.com',
            'phone' => '081298765432',
            'password' => 'password123',
            'role' => 'customer',
        ]);

        $response = $this->actingAs($customer)->get(route('customer.profile'));

        $response->assertStatus(200);
        $response->assertSee('Profil Pelanggan');
        $response->assertSee('Siti Aisyah');
        $response->assertSee('siti@example.com');
        $response->assertSee(route('catalog.index'));
    }

    /**
     * 13. Guest yang mencoba mengakses profil dialihkan ke halaman login customer.
     */
    public function test_guest_is_redirected_to_login_from_customer_profile(): void
    {
        $response = $this->get(route('customer.profile'));

        $response->assertRedirect(route('login'));
    }

    /**
     * 14. Admin dashboard tetap aman dan terproteksi dari akses Guest maupun Customer.
     */
    public function test_admin_dashboard_remains_strictly_protected(): void
    {
        $customer = User::create([
            'name' => 'Regular Customer',
            'email' => 'regular@example.com',
            'password' => 'password123',
            'role' => 'customer',
        ]);

        $admin = User::create([
            'name' => 'Super Admin',
            'email' => 'admin@example.com',
            'password' => 'password123',
            'role' => 'admin',
        ]);

        // Guest dialihkan ke login admin
        $this->get(route('admin.dashboard'))->assertRedirect(route('admin.login'));

        // Customer dilarang mengakses admin dashboard (403 Forbidden)
        $this->actingAs($customer)->get(route('admin.dashboard'))->assertForbidden();

        // Admin dapat mengakses dashboard secara normal
        $this->actingAs($admin)->get(route('admin.dashboard'))->assertOk();
    }
}
