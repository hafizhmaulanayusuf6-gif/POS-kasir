<?php

namespace Tests\Feature;

use App\Models\Produk;
use App\Models\Transaksi;
use App\Models\TransaksiDetail;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class KasirCheckoutTest extends TestCase
{
    use RefreshDatabase;

    private User $kasir;

    /**
     * PENGAMAN: RefreshDatabase menghapus seluruh isi database.
     * Hook ini berjalan SEBELUM database di-refresh, jadi test langsung
     * berhenti kalau koneksi test bukan SQLite in-memory (bukan MySQL Anda).
     */
    protected function beforeRefreshingDatabase()
    {
        if (config('database.default') !== 'sqlite'
            || config('database.connections.sqlite.database') !== ':memory:') {
            throw new \RuntimeException(
                'Test dihentikan: koneksi test bukan SQLite in-memory. Cek phpunit.xml.'
            );
        }
    }

    protected function setUp(): void
    {
        parent::setUp();

        // User biasa (role default 'user'), sama seperti kasir sungguhan.
        $this->kasir = User::factory()->create();
    }

    private function buatProduk(array $override = []): Produk
    {
        return Produk::create(array_merge([
            'nama_produk' => 'Kopi Susu',
            'harga' => 10000,
            'stok' => 10,
        ], $override));
    }

    /** Item cart dalam format yang dikirim JavaScript halaman kasir. */
    private function item(Produk $p, array $override = []): array
    {
        return array_merge([
            'id' => $p->id,
            'nama' => $p->nama_produk,
            'harga' => $p->harga,
            'jumlah' => 1,
            'stok' => $p->stok,
        ], $override);
    }

    private function checkout(array $cart, string $metode = 'qris', ?int $bayar = null)
    {
        $data = ['cart' => json_encode($cart), 'metode_bayar' => $metode];

        if ($bayar !== null) {
            $data['bayar'] = $bayar;
        }

        return $this->actingAs($this->kasir)->post('/kasir', $data);
    }

    // =====================================================================
    // A. PERILAKU YANG SUDAH BERJALAN (harus lulus SEBELUM dan SESUDAH perbaikan)
    // =====================================================================

    public function test_tamu_tidak_bisa_checkout(): void
    {
        $produk = $this->buatProduk();

        $this->post('/kasir', [
            'cart' => json_encode([$this->item($produk)]),
            'metode_bayar' => 'qris',
        ])->assertRedirect(route('login'));

        $this->assertSame(0, Transaksi::count());
    }

    public function test_checkout_cash_berhasil(): void
    {
        $produk = $this->buatProduk(['harga' => 10000, 'stok' => 10]);

        $response = $this->checkout([$this->item($produk, ['jumlah' => 3])], 'cash', 50000);

        $response->assertRedirect(route('kasir.index'));
        $response->assertSessionHas('success', fn ($v) => str_contains($v, 'TRX-') && str_contains($v, 'Rp 20.000'));

        $trx = Transaksi::firstOrFail();
        $this->assertSame($this->kasir->id, $trx->user_id);
        $this->assertSame('cash', $trx->metode_bayar);
        $this->assertSame(30000, $trx->total_bayar);
        $this->assertSame(50000, $trx->bayar);
        $this->assertSame(20000, $trx->kembalian);
        $this->assertMatchesRegularExpression('/^TRX-\d{8}-0001$/', $trx->kode_transaksi);

        $detail = TransaksiDetail::firstOrFail();
        $this->assertSame($trx->id, $detail->transaksi_id);
        $this->assertSame($produk->id, $detail->produk_id);
        $this->assertSame('Kopi Susu', $detail->nama_produk);
        $this->assertSame(10000, $detail->harga);
        $this->assertSame(3, $detail->jumlah);
        $this->assertSame(30000, $detail->subtotal);

        $this->assertSame(7, $produk->fresh()->stok);
    }

    #[DataProvider('metodeNonTunai')]
    public function test_checkout_non_tunai_dibayar_pas(string $metode): void
    {
        $produk = $this->buatProduk(['harga' => 10000, 'stok' => 10]);

        $this->checkout([$this->item($produk, ['jumlah' => 2])], $metode)
            ->assertRedirect(route('kasir.index'));

        $trx = Transaksi::firstOrFail();
        $this->assertSame($metode, $trx->metode_bayar);
        $this->assertSame(20000, $trx->total_bayar);
        $this->assertSame(20000, $trx->bayar);
        $this->assertSame(0, $trx->kembalian);
        $this->assertSame(8, $produk->fresh()->stok);
    }

    public static function metodeNonTunai(): array
    {
        return [
            'qris' => ['qris'],
            'transfer' => ['transfer'],
        ];
    }

    public function test_kode_transaksi_berurutan(): void
    {
        $produk = $this->buatProduk();

        $this->checkout([$this->item($produk)]);
        $this->checkout([$this->item($produk)]);

        $kode = Transaksi::orderBy('id')->pluck('kode_transaksi')->all();

        $this->assertMatchesRegularExpression('/^TRX-\d{8}-0001$/', $kode[0]);
        $this->assertMatchesRegularExpression('/^TRX-\d{8}-0002$/', $kode[1]);
    }

    public function test_keranjang_kosong_ditolak(): void
    {
        $this->checkout([])
            ->assertSessionHas('error', 'Keranjang masih kosong.');

        $this->assertSame(0, Transaksi::count());
    }

    public function test_stok_tidak_cukup_ditolak(): void
    {
        $produk = $this->buatProduk(['stok' => 2]);

        $this->checkout([$this->item($produk, ['jumlah' => 5])])
            ->assertSessionHas('error');

        $this->assertSame(0, Transaksi::count());
        $this->assertSame(2, $produk->fresh()->stok);
    }

    public function test_uang_bayar_kurang_ditolak(): void
    {
        $produk = $this->buatProduk(['harga' => 10000, 'stok' => 10]);

        $this->checkout([$this->item($produk, ['jumlah' => 3])], 'cash', 10000)
            ->assertSessionHas('error', 'Uang bayar kurang dari total belanja.');

        $this->assertSame(0, Transaksi::count());
        $this->assertSame(10, $produk->fresh()->stok);
    }

    public function test_produk_yang_tidak_ada_ditolak(): void
    {
        $this->checkout([[
            'id' => 99999, 'nama' => 'Hantu', 'harga' => 1000, 'jumlah' => 1, 'stok' => 1,
        ]])->assertSessionHas('error');

        $this->assertSame(0, Transaksi::count());
    }

    // =====================================================================
    // B. CELAH KEAMANAN (diperkirakan GAGAL sebelum perbaikan, LULUS sesudahnya)
    // =====================================================================

    public function test_harga_dari_browser_diabaikan_harga_diambil_dari_database(): void
    {
        $produk = $this->buatProduk(['harga' => 10000, 'stok' => 10]);

        // Browser dimanipulasi: harga dikirim Rp 1
        $this->checkout([$this->item($produk, ['harga' => 1, 'jumlah' => 2])]);

        $trx = Transaksi::firstOrFail();
        $this->assertSame(20000, $trx->total_bayar);

        $detail = TransaksiDetail::firstOrFail();
        $this->assertSame(10000, $detail->harga);
        $this->assertSame(20000, $detail->subtotal);
    }

    public function test_nama_produk_di_detail_diambil_dari_database(): void
    {
        $produk = $this->buatProduk(['nama_produk' => 'Kopi Susu']);

        $this->checkout([$this->item($produk, ['nama' => 'Nama Palsu'])]);

        $this->assertSame('Kopi Susu', TransaksiDetail::firstOrFail()->nama_produk);
    }

    public function test_jumlah_negatif_ditolak(): void
    {
        $produk = $this->buatProduk(['stok' => 10]);

        $this->checkout([$this->item($produk, ['jumlah' => -5])])
            ->assertSessionHas('error');

        $this->assertSame(0, Transaksi::count());
        $this->assertSame(10, $produk->fresh()->stok);
    }

    public function test_jumlah_nol_dan_pecahan_ditolak(): void
    {
        $produk = $this->buatProduk(['stok' => 10]);

        $this->checkout([$this->item($produk, ['jumlah' => 0])])
            ->assertSessionHas('error');

        $this->checkout([$this->item($produk, ['jumlah' => 1.5])])
            ->assertSessionHas('error');

        $this->assertSame(0, Transaksi::count());
        $this->assertSame(10, $produk->fresh()->stok);
    }

    public function test_produk_yang_sama_dua_baris_tidak_bisa_melewati_stok(): void
    {
        $produk = $this->buatProduk(['stok' => 5]);

        // Dua baris produk yang sama, masing-masing 4 (total 8 > stok 5)
        $this->checkout([
            $this->item($produk, ['jumlah' => 4]),
            $this->item($produk, ['jumlah' => 4]),
        ])->assertSessionHas('error');

        $this->assertSame(0, Transaksi::count());
        $this->assertSame(5, $produk->fresh()->stok);
    }
}