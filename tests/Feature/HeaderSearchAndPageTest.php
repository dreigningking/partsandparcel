<?php

namespace Tests\Feature;

use App\Livewire\HeaderSearch;
use App\Models\Brand;
use App\Models\Category;
use App\Models\DeviceModel;
use App\Models\Discussion;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class HeaderSearchAndPageTest extends TestCase
{
    use RefreshDatabase;

    public function test_header_search_renders_and_returns_suggestions()
    {
        $cat = Category::create([
            'name' => 'Smartphones & Tablets',
            'slug' => 'phones',
        ]);

        $brand = Brand::create([
            'name' => 'Apple',
            'slug' => 'apple',
        ]);

        $model = DeviceModel::create([
            'category_id' => $cat->id,
            'brand_id' => $brand->id,
            'name' => 'iPhone 12',
            'slug' => 'apple-iphone-12',
        ]);

        Livewire::test(HeaderSearch::class)
            ->set('query', 'iphone')
            ->assertSee('iPhone 12 — Apple / Smartphones & Tablets')
            ->assertSee('model suggestion');
    }

    public function test_header_search_submits_and_redirects_to_search_page()
    {
        Livewire::test(HeaderSearch::class)
            ->set('query', 'MacBook')
            ->call('submitSearch')
            ->assertRedirect(route('search', ['q' => 'MacBook']));
    }

    public function test_search_page_renders_matching_items_and_discussions()
    {
        $user = User::factory()->create();

        $cat = Category::create([
            'name' => 'Laptops & Computers',
            'slug' => 'laptops',
        ]);

        $model = DeviceModel::create([
            'category_id' => $cat->id,
            'name' => 'ThinkPad X1',
            'slug' => 'thinkpad-x1',
        ]);

        Item::create([
            'user_id' => $user->id,
            'model_id' => $model->id,
            'name' => 'Lenovo ThinkPad X1 Carbon',
            'description' => 'Core i7 16GB RAM 512GB SSD',
            'condition_status' => 'used',
            'status' => 'available',
        ]);

        Discussion::create([
            'user_id' => $user->id,
            'category_id' => $cat->id,
            'title' => 'Looking for ThinkPad X1 battery replacement',
            'body' => 'Need genuine battery in Lagos',
            'status' => 'open',
        ]);

        $response = $this->get(route('search', ['q' => 'ThinkPad']));

        $response->assertStatus(200);
        $response->assertSee('Search Results for');
        $response->assertSee('ThinkPad');
        $response->assertSee('Lenovo ThinkPad X1 Carbon');
        $response->assertSee('Looking for ThinkPad X1 battery replacement');
    }
}
