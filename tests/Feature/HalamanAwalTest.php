<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HalamanAwalTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    private function kasir(): User
    {
        return User::factory()->create(); // role default 'user'
    }

    private function login(User $user, array $session = [])
    {
        return $this->withSession($session)->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);
    }

    public function test_kasir_setelah_login_diarahkan_ke_halaman_kasir(): void
    {
        $kasir = $this->kasir();

        $this->login($kasir)->assertRedirect(route('kasir.index', absolute: false));

        // Halaman tujuannya benar-benar bisa dibuka kasir (bukan 403)
        $this->actingAs($kasir)->get('/kasir')->assertOk();
    }

    public function test_admin_setelah_login_diarahkan_ke_dashboard(): void
    {
        $admin = $this->admin();

        $this->login($admin)->assertRedirect(route('dashboard', absolute: false));

        $this->actingAs($admin)->get('/dashboard')->assertOk();
    }

    public function test_halaman_tujuan_sebelum_login_tetap_dihormati(): void
    {
        $this->login($this->kasir(), ['url.intended' => url('/riwayat-transaksi')])
            ->assertRedirect(url('/riwayat-transaksi'));
    }

    public function test_kasir_yang_sudah_login_dan_membuka_login_tidak_dilempar_ke_halaman_admin(): void
    {
        $this->actingAs($this->kasir())
            ->get('/login')
            ->assertRedirect(route('kasir.index'));
    }

    public function test_admin_yang_sudah_login_dan_membuka_login_diarahkan_ke_dashboard(): void
    {
        $this->actingAs($this->admin())
            ->get('/login')
            ->assertRedirect(route('dashboard'));
    }
}