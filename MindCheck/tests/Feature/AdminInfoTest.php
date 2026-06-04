<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminInfoTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthenticated_admin_is_redirected(): void
    {
        $response = $this->get(route('admin.info'));

        $response->assertRedirect(route('admin.login'));
    }

    public function test_authenticated_admin_can_view_info_page(): void
    {
        $response = $this->withSession(['admin_id' => 1, 'admin_name' => 'Test Admin'])
            ->get(route('admin.info'));

        $response->assertStatus(200);
        $response->assertSee('Informasi Sistem');
        $response->assertSee('Test Admin');
        $response->assertSee('DASS-21');
    }
}
