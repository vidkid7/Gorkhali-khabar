<?php

namespace Tests\Feature\Api;

use App\Models\EditorialMenu;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EditorialMenuTest extends TestCase
{
    use RefreshDatabase;

    public function test_menu_location_can_be_selected_with_query_string(): void
    {
        EditorialMenu::query()->create([
            'location' => 'header',
            'label' => 'Header link',
            'href' => '/header',
            'sort_order' => 10,
            'is_active' => true,
        ]);
        EditorialMenu::query()->create([
            'location' => 'footer',
            'label' => 'Footer link',
            'href' => '/footer',
            'sort_order' => 10,
            'is_active' => true,
        ]);

        $this->getJson('/api/v1/menus?location=footer')
            ->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.href', '/footer');
    }

    public function test_menu_location_can_be_selected_with_path_parameter(): void
    {
        EditorialMenu::query()->create([
            'location' => 'header',
            'label' => 'Header link',
            'href' => '/header',
            'sort_order' => 10,
            'is_active' => true,
        ]);

        $this->getJson('/api/v1/menus/header')
            ->assertOk()
            ->assertJsonPath('data.0.href', '/header');
    }
}
