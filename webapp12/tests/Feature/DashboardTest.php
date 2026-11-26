<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_the_login_page(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_authenticated_users_can_visit_the_dashboard(): void
    {
        $user = User::factory()->create(['two_factor_confirmed_at' => now()]);
        $this->actingAs($user)->withSession(['totp_passed_for' => $user->id]);

        $response = $this->get('/dashboard');
        $response->assertStatus(200);
    }
}
