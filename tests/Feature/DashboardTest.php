<?php

namespace Tests\Feature;

use App\Models\Area;
use App\Models\Category;
use App\Models\MonthlyTarget;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Store $store;
    private Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $area = Area::create(['name' => 'テストエリア', 'region' => 'テスト']);
        $this->store = Store::create([
            'area_id' => $area->id,
            'name' => 'テスト店舗',
            'code' => 'TEST-001',
        ]);
        $category = Category::create(['name' => 'テストカテゴリ', 'sort_order' => 1]);
        $this->product = Product::create([
            'category_id' => $category->id,
            'name' => 'テスト商品',
            'code' => 'P-001',
            'price' => 100000,
        ]);
        $this->user = User::factory()->create(['role' => 'admin']);
    }

    public function test_ダッシュボードが表示される(): void
    {
        $response = $this->actingAs($this->user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('ダッシュボード');
    }

    public function test_未ログインはログイン画面にリダイレクト(): void
    {
        $response = $this->get('/dashboard');

        $response->assertRedirect('/login');
    }

    public function test_KPIが正しく計算される(): void
    {
        Sale::create([
            'store_id' => $this->store->id,
            'product_id' => $this->product->id,
            'quantity' => 2,
            'unit_price' => 100000,
            'total' => 200000,
            'sale_date' => now()->format('Y-m-d'),
        ]);

        $response = $this->actingAs($this->user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('200,000');
    }

    public function test_売上データなしでもダッシュボードが表示される(): void
    {
        $response = $this->actingAs($this->user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('¥0');
    }
}