<?php

namespace Tests\Feature;

use App\Models\Transaksi;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class OmzetBulananTest extends TestCase
{
    use RefreshDatabase;

    private int $urutKode = 0;

    protected function setUp(): void
    {
        parent::setUp();

        // Kunci "hari ini" agar test tidak bergantung pada tanggal saat dijalankan.
        $this->travelTo(Carbon::create(2026, 10, 15, 12, 0, 0));
    }

    private function admin(): User
    {
        return User::factory()->create(['role' => 'admin']);
    }

    private function kasir(): User
    {
        return User::factory()->create(); // role default 'user'
    }

    private function buatTransaksi(User $user, string $waktu, int $total): Transaksi
    {
        $trx = new Transaksi([
            'kode_transaksi' => 'TRX-TEST-' . (++$this->urutKode),
            'user_id' => $user->id,
            'metode_bayar' => 'cash',
            'total_bayar' => $total,
            'bayar' => $total,
            'kembalian' => 0,
        ]);
        $trx->created_at = $waktu;
        $trx->updated_at = $waktu;
        $trx->save();

        return $trx;
    }

    private function isiDataUji(User $user): void
    {
        $this->buatTransaksi($user, '2026-01-10 10:00:00', 100000);
        $this->buatTransaksi($user, '2026-01-25 15:00:00', 50000);
        $this->buatTransaksi($user, '2026-03-31 23:59:00', 200000); // batas akhir Maret
        $this->buatTransaksi($user, '2026-04-01 00:00:00', 30000);  // batas awal April
        $this->buatTransaksi($user, '2026-12-31 23:59:59', 75000);  // batas akhir tahun
        $this->buatTransaksi($user, '2025-12-31 23:59:59', 999);    // tahun lain
    }

    // ---------- Admin ----------

    public function test_admin_melihat_omzet_per_bulan_yang_benar(): void
    {
        $admin = $this->admin();
        $this->isiDataUji($admin);

        $this->actingAs($admin)
            ->get('/dashboard-kasir')
            ->assertOk()
            ->assertViewHas('tahunDipilih', 2026)
            ->assertViewHas('labelBulanan', ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'])
            ->assertViewHas('dataBulanan', [150000, 0, 200000, 30000, 0, 0, 0, 0, 0, 0, 0, 75000])
            ->assertViewHas('totalOmzetTahun', 455000)
            ->assertSee('Omzet Bulanan 2026')
            ->assertSee('chartOmzetBulanan', false)
            // Bagian lama dashboard tetap ada
            ->assertSee('Omzet 7 Hari Terakhir');
    }

    public function test_admin_bisa_memilih_tahun_lain(): void
    {
        $admin = $this->admin();
        $this->isiDataUji($admin);

        $this->actingAs($admin)
            ->get('/dashboard-kasir?tahun=2025')
            ->assertOk()
            ->assertViewHas('tahunDipilih', 2025)
            ->assertViewHas('daftarTahun', [2026, 2025])
            ->assertViewHas('dataBulanan', [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 999]);
    }

    public function test_tahun_tidak_valid_kembali_ke_tahun_berjalan(): void
    {
        $admin = $this->admin();
        $this->isiDataUji($admin);

        foreach (['abc', '1900', '2999', '0'] as $tahun) {
            $this->actingAs($admin)
                ->get('/dashboard-kasir?tahun=' . $tahun)
                ->assertOk()
                ->assertViewHas('tahunDipilih', 2026);
        }
    }

    public function test_admin_tanpa_transaksi_melihat_chart_kosong(): void
    {
        $this->actingAs($this->admin())
            ->get('/dashboard-kasir')
            ->assertOk()
            ->assertViewHas('tahunDipilih', 2026)
            ->assertViewHas('daftarTahun', [2026])
            ->assertViewHas('dataBulanan', array_fill(0, 12, 0))
            ->assertViewHas('totalOmzetTahun', 0);
    }

    // ---------- Kasir ----------

    public function test_kasir_tidak_melihat_chart_bulanan(): void
    {
        $kasir = $this->kasir();
        $this->isiDataUji($kasir);

        $this->actingAs($kasir)
            ->get('/dashboard-kasir')
            ->assertOk()
            ->assertDontSee('Omzet Bulanan')
            ->assertDontSee('chartOmzetBulanan', false)
            ->assertViewMissing('dataBulanan')
            ->assertViewMissing('totalOmzetTahun')
            // Tampilan lama tetap seperti sebelumnya
            ->assertSee('Omzet 7 Hari Terakhir');
    }
}