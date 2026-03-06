<?php

namespace Tests\Feature;

use App\Models\Area;
use App\Models\Category;
use App\Models\Product;
use App\Models\Sale;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SaleTest extends TestCase
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

    public function test_売上一覧が表示される(): void
    {
        $response = $this->actingAs($this->user)->get('/sales');

        $response->assertStatus(200);
        $response->assertSee('売上一覧');
    }

    public function test_売上を登録できる(): void
    {
        $response = $this->actingAs($this->user)->post('/sales', [
            'store_id' => $this->store->id,
            'product_id' => $this->product->id,
            'quantity' => 3,
            'sale_date' => '2026-03-01',
        ]);

        $response->assertRedirect('/sales');
        $this->assertDatabaseHas('sales', [
            'store_id' => $this->store->id,
            'product_id' => $this->product->id,
            'quantity' => 3,
            'unit_price' => 100000,
            'total' => 300000,
        ]);
    }

    public function test_売上を更新できる(): void
    {
        $sale = Sale::create([
            'store_id' => $this->store->id,
            'product_id' => $this->product->id,
            'quantity' => 1,
            'unit_price' => 100000,
            'total' => 100000,
            'sale_date' => '2026-03-01',
        ]);

        $response = $this->actingAs($this->user)->put("/sales/{$sale->id}", [
            'store_id' => $this->store->id,
            'product_id' => $this->product->id,
            'quantity' => 5,
            'sale_date' => '2026-03-02',
        ]);

        $response->assertRedirect('/sales');
        $this->assertDatabaseHas('sales', [
            'id' => $sale->id,
            'quantity' => 5,
            'total' => 500000,
        ]);
    }

    public function test_売上を削除できる(): void
    {
        $sale = Sale::create([
            'store_id' => $this->store->id,
            'product_id' => $this->product->id,
            'quantity' => 1,
            'unit_price' => 100000,
            'total' => 100000,
            'sale_date' => '2026-03-01',
        ]);

        $response = $this->actingAs($this->user)->delete("/sales/{$sale->id}");

        $response->assertRedirect('/sales');
        $this->assertDatabaseMissing('sales', ['id' => $sale->id]);
    }

    public function test_店舗で絞り込みできる(): void
    {
        Sale::create([
            'store_id' => $this->store->id,
            'product_id' => $this->product->id,
            'quantity' => 1,
            'unit_price' => 100000,
            'total' => 100000,
            'sale_date' => '2026-03-01',
        ]);

        $response = $this->actingAs($this->user)->get('/sales?store_id=' . $this->store->id);

        $response->assertStatus(200);
        $response->assertSee('テスト店舗');
    }

    public function test_バリデーションエラーで登録できない(): void
    {
        $response = $this->actingAs($this->user)->post('/sales', [
            'store_id' => '',
            'product_id' => '',
            'quantity' => 0,
            'sale_date' => '',
        ]);

        $response->assertSessionHasErrors(['store_id', 'product_id', 'quantity', 'sale_date']);
    }

    public function test_Excelエクスポートができる(): void
    {
        Sale::create([
            'store_id' => $this->store->id,
            'product_id' => $this->product->id,
            'quantity' => 1,
            'unit_price' => 100000,
            'total' => 100000,
            'sale_date' => '2026-03-01',
        ]);

        $response = $this->actingAs($this->user)->get('/sales-export?format=xlsx');

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    public function test_CSVエクスポートができる(): void
    {
        $response = $this->actingAs($this->user)->get('/sales-export?format=csv');

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'text/csv; charset=utf-8');
    }
}