<?php

namespace Tests\Feature;

use App\Models\User;
use App\Support\Totp;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class TotpTest extends TestCase
{
    use RefreshDatabase;

    public function test_sin_totp_confirmado_se_redirige_al_enrolamiento(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->get('/dashboard')->assertRedirect(route('totp.setup'));
    }

    public function test_enrolamiento_valido_da_acceso_y_el_codigo_no_se_reutiliza(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('totp.setup'))->assertOk()->assertSee('otpauth://totp/', false);
        $user->refresh();

        $code = Totp::code($user->two_factor_secret);
        $this->post(route('totp.confirm'), ['code' => '000000'])->assertSessionHasErrors('code');
        $this->post(route('totp.confirm'), ['code' => $code])->assertRedirect(route('dashboard', absolute: false));
        $this->get('/dashboard')->assertOk();

        $this->flushSession();
        $this->get('/dashboard')->assertRedirect(route('totp.challenge'));
        $this->post(route('totp.verify'), ['code' => $code])->assertSessionHasErrors('code');
    }

    public function test_el_secreto_se_guarda_cifrado(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->get(route('totp.setup'));
        $raw = DB::table('users')->where('id', $user->id)->value('two_factor_secret');
        $this->assertNotSame($user->fresh()->two_factor_secret, $raw);
    }

    public function test_vector_rfc_6238(): void
    {
        $this->assertSame('287082', Totp::code('GEZDGNBVGY3TQOJQGEZDGNBVGY3TQOJQ', intdiv(59, 30)));
    }
}
