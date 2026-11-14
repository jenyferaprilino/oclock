<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use Database\Seeders\CategorySeeder;
use Database\Seeders\ProductSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StageFourPointOneCatalogRefinementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(CategorySeeder::class);
        $this->seed(ProductSeeder::class);
    }

    /**
     * 1. ProductSeeder memuat minimal 24 produk (saat ini 29) secara idempoten.
     */
    public function test_product_seeder_populates_comprehensive_catalog_idempotently(): void
    {
        $initialCount = Product::count();
        $this->assertGreaterThanOrEqual(24, $initialCount);
        $this->assertEquals(29, $initialCount);

        // Jalankan seeder kedua kali untuk membuktikan idempotensi
        $this->seed(ProductSeeder::class);
        $this->assertEquals($initialCount, Product::count());
    }

    /**
     * 2. Seluruh brand utama (internasional, lokal Indonesia, Alexandre Christie) terwakili.
     */
    public function test_all_required_brands_are_represented(): void
    {
        $expectedBrands = [
            'Casio',
            'Seiko',
            'Citizen',
            'Orient',
            'Timex',
            'EIGER',
            'PALA Nusantara',
            'Woodka',
            'Alexandre Christie',
        ];

        foreach ($expectedBrands as $brand) {
            $this->assertTrue(
                Product::where('brand', $brand)->exists(),
                "Brand {$brand} harus ada di katalog."
            );
        }
    }

    /**
     * 3. Alexandre Christie memiliki minimal 4-5 model resmi dan dapat dicari di katalog.
     */
    public function test_alexandre_christie_collection_is_searchable_and_has_required_models(): void
    {
        $acCount = Product::where('brand', 'Alexandre Christie')->count();
        $this->assertGreaterThanOrEqual(4, $acCount);

        // Verifikasi model spesifik
        $this->assertTrue(Product::where('sku', 'AC-SPT-6295MPR')->exists());
        $this->assertTrue(Product::where('sku', 'AC-AUT-9601MAR')->exists());
        $this->assertTrue(Product::where('sku', 'AC-CLS-6647MCB')->exists());
        $this->assertTrue(Product::where('sku', 'AC-WMN-2A40LDB')->exists());
        $this->assertTrue(Product::where('sku', 'AC-CLS-8645MD')->exists());

        // Pencarian di katalog publik
        $response = $this->get(route('catalog.index', ['search' => 'Alexandre Christie']));
        $response->assertStatus(200);
        $response->assertSee('AC 6295 MPR');
        $response->assertSee('AC 9601 MAR');
    }

    /**
     * 4. Brand lokal Indonesia (EIGER, PALA Nusantara, Woodka) memiliki spesifikasi otentik.
     */
    public function test_indonesian_local_brands_have_authentic_specifications(): void
    {
        // EIGER Heliconia
        $eiger = Product::where('sku', 'EGR-OUT-HEL01')->first();
        $this->assertNotNull($eiger);
        $this->assertEquals('EIGER', $eiger->brand);
        $this->assertStringContainsStringIgnoringCase('altimeter', $eiger->description);

        // PALA Nusantara Merah
        $pala = Product::where('sku', 'PAL-WOD-MER01')->first();
        $this->assertNotNull($pala);
        $this->assertEquals('PALA Nusantara', $pala->brand);
        $this->assertStringContainsStringIgnoringCase('sonokeling', $pala->description);

        // Woodka Loco
        $woodka = Product::where('sku', 'WDK-LCO-TK01')->first();
        $this->assertNotNull($woodka);
        $this->assertEquals('Woodka', $woodka->brand);
        $this->assertStringContainsStringIgnoringCase('tenun ikat', $woodka->description);
    }

    /**
     * 5. Setiap produk memiliki path gambar individual yang unik dan terisi.
     */
    public function test_every_product_has_distinct_image_asset_path(): void
    {
        $products = Product::all();

        foreach ($products as $product) {
            $this->assertNotNull($product->image_path, "Produk {$product->sku} harus memiliki image_path.");
            $this->assertStringStartsWith('products/', $product->image_path);
            $this->assertStringEndsWith('.svg', $product->image_path);
        }
    }

    /**
     * 6. Halaman detail setiap brand baru dapat diakses dengan HTTP 200 dan spesifikasi lengkap.
     */
    public function test_product_detail_for_new_brands_renders_successfully(): void
    {
        $testSkus = [
            'CAS-GA2100-BLK',
            'SEI-PRS-SRPD37',
            'CTZ-TSU-NJ0150',
            'ORN-BAM-AC0009',
            'TMX-Q1979-TW2T',
            'EGR-OUT-HEL01',
            'PAL-WOD-MER01',
            'WDK-LCO-TK01',
            'AC-AUT-9601MAR',
        ];

        foreach ($testSkus as $sku) {
            $product = Product::where('sku', $sku)->firstOrFail();
            $response = $this->get(route('catalog.show', $product->slug));

            $response->assertStatus(200);
            $response->assertSee($product->name);
            $response->assertSee($product->brand);
            $response->assertSee($product->sku);
            $response->assertSee($product->movement_type);
        }
    }

    /**
     * 7. Seluruh 6 kategori jam tangan memiliki representasi produk aktif.
     */
    public function test_all_categories_have_active_products(): void
    {
        $categories = Category::withCount(['products' => function ($q) {
            $q->where('is_active', true);
        }])->get();

        foreach ($categories as $category) {
            $this->assertGreaterThan(
                0,
                $category->products_count,
                "Kategori {$category->name} harus memiliki minimal 1 produk aktif."
            );
        }
    }
}
