<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KaryawanTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    private function kasir(array $override = []): User
    {
        return User::factory()->create($override); // role default 'user'
    }

    public function test_akun_baru_aktif_secara_default(): void
    {
        $user = $this->kasir();

        $this->assertTrue($user->fresh()->is_active);
    }

    // ---------- Akses halaman ----------

    public function test_tamu_dialihkan_ke_login(): void
    {
        $this->get('/karyawan')->assertRedirect(route('login'));
    }

    public function test_kasir_tidak_bisa_membuka_halaman_kelola_karyawan(): void
    {
        $this->actingAs($this->kasir())->get('/karyawan')->assertForbidden();
    }

    public function test_admin_bisa_membuka_dan_melihat_daftar_karyawan(): void
    {
        $kasir = $this->kasir();

        $this->actingAs($this->admin())
            ->get('/karyawan')
            ->assertOk()
            ->assertSee($kasir->email);
    }

    // ---------- Aktif / nonaktif ----------

    public function test_admin_bisa_menonaktifkan_karyawan(): void
    {
        $kasir = $this->kasir();

        $this->actingAs($this->admin())
            ->patch(route('karyawan.status', $kasir))
            ->assertSessionHas('success');

        $this->assertFalse($kasir->fresh()->is_active);
    }

    public function test_admin_bisa_mengaktifkan_kembali_karyawan(): void
    {
        $kasir = $this->kasir(['is_active' => false]);

        $this->actingAs($this->admin())
            ->patch(route('karyawan.status', $kasir))
            ->assertSessionHas('success');

        $this->assertTrue($kasir->fresh()->is_active);
    }

    public function test_admin_tidak_bisa_menonaktifkan_diri_sendiri(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->patch(route('karyawan.status', $admin))
            ->assertSessionHas('error');

        $this->assertTrue($admin->fresh()->is_active);
    }

    public function test_kasir_tidak_bisa_mengubah_status_karyawan(): void
    {
        $target = $this->kasir();

        $this->actingAs($this->kasir())
            ->patch(route('karyawan.status', $target))
            ->assertForbidden();

        $this->assertTrue($target->fresh()->is_active);
    }

    // ---------- Penegakan ----------

    public function test_akun_nonaktif_tidak_bisa_login(): void
    {
        $kasir = $this->kasir(['is_active' => false]);

        $this->post('/login', [
            'email' => $kasir->email,
            'password' => 'password',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_sesi_akun_yang_dinonaktifkan_terputus(): void
    {
        $kasir = $this->kasir();

        // Kasir sedang login...
        $this->actingAs($kasir);

        // ...lalu admin menonaktifkannya
        $kasir->is_active = false;
        $kasir->save();

        $this->get('/kasir')->assertRedirect(route('login'));
        $this->assertGuest();
    }
}