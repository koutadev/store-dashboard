<?php

namespace Tests\Feature;

use App\Models\Area;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private Area $area;

    protected function setUp(): void
    {
        parent::setUp();

        $this->area = Area::create(['name' => 'テストエリア', 'region' => 'テスト']);
        $this->user = User::factory()->create(['role' => 'admin']);
    }

    public function test_店舗一覧が表示される(): void
    {
        Store::create([
            'area_id' => $this->area->id,
            'name' => 'テスト店舗',
            'code' => 'TEST-001',
        ]);

        $response = $this->actingAs($this->user)->get('/stores');

        $response->assertStatus(200);
        $response->assertViewHas('stores', function ($stores) {
            return $stores->contains('code', 'TEST-001');
        });
    }

    public function test_店舗を登録できる(): void
    {
        $response = $this->actingAs($this->user)->post('/stores', [
            'area_id' => $this->area->id,
            'name' => '新規店舗',
            'code' => 'NEW-001',
            'address' => '東京都渋谷区1-1-1',
            'phone' => '03-9999-9999',
            'is_active' => true,
        ]);

        $response->assertRedirect('/stores');
        $this->assertDatabaseHas('stores', [
            'name' => '新規店舗',
            'code' => 'NEW-001',
        ]);
    }

    public function test_店舗を更新できる(): void
    {
        $store = Store::create([
            'area_id' => $this->area->id,
            'name' => '更新前店舗',
            'code' => 'UPD-001',
        ]);

        $response = $this->actingAs($this->user)->put("/stores/{$store->id}", [
            'area_id' => $this->area->id,
            'name' => '更新後店舗',
            'code' => 'UPD-001',
            'is_active' => true,
        ]);

        $response->assertRedirect('/stores');
        $this->assertDatabaseHas('stores', [
            'id' => $store->id,
            'name' => '更新後店舗',
        ]);
    }

    public function test_店舗を削除できる(): void
    {
        $store = Store::create([
            'area_id' => $this->area->id,
            'name' => '削除店舗',
            'code' => 'DEL-001',
        ]);

        $response = $this->actingAs($this->user)->delete("/stores/{$store->id}");

        $response->assertRedirect('/stores');
        $this->assertDatabaseMissing('stores', ['id' => $store->id]);
    }

    public function test_店舗コードが重複するとエラー(): void
    {
        Store::create([
            'area_id' => $this->area->id,
            'name' => '既存店舗',
            'code' => 'DUP-001',
        ]);

        $response = $this->actingAs($this->user)->post('/stores', [
            'area_id' => $this->area->id,
            'name' => '重複店舗',
            'code' => 'DUP-001',
        ]);

        $response->assertSessionHasErrors(['code']);
    }

    public function test_エリアで絞り込みできる(): void
    {
        Store::create([
            'area_id' => $this->area->id,
            'name' => '絞込店舗',
            'code' => 'FLT-001',
        ]);

        $response = $this->actingAs($this->user)->get('/stores?area_id=' . $this->area->id);

        $response->assertStatus(200);
        $response->assertViewHas('stores', function ($stores) {
            return $stores->contains('code', 'FLT-001');
        });
    }
}