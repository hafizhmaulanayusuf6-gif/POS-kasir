<?php

namespace App\Http\Controllers;

use App\Models\Transaksi;
use App\Models\TransaksiDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DashboardKasirController extends Controller
{
    private const NAMA_BULAN = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];

    public function index(Request $request)
    {
        $omzetHariIni = Transaksi::whereDate('created_at', today())->sum('total_bayar');
        $jumlahTransaksiHariIni = Transaksi::whereDate('created_at', today())->count();


        $produkTerlaris = TransaksiDetail::selectRaw('nama_produk, SUM(jumlah) as total_terjual')
            ->groupBy('nama_produk')
            ->orderByDesc('total_terjual')
            ->limit(5)
            ->get();

        $metodeBayar = Transaksi::selectRaw('metode_bayar, COUNT(*) as jumlah')
            ->whereDate('created_at', today())
            ->groupBy('metode_bayar')
            ->get();

        $transaksiTerbaru = Transaksi::with('user')->latest()->limit(5)->get();

        // data omzet 7 hari terakhir untuk chart
        $omzetMingguan = Transaksi::selectRaw('DATE(created_at) as tanggal, SUM(total_bayar) as total')
            ->where('created_at', '>=', now()->subDays(6)->startOfDay())
            ->groupBy('tanggal')
            ->orderBy('tanggal')
            ->get();

        // Susun untuk 7 hari terakhir, isi 0 kalau tidak ada transaksi hari itu
        $labelChart = [];
        $dataChart = [];
        for ($i = 6; $i >= 0; $i-- ) {
            $tanggal = now()->subDays($i)->format('Y-m-d');
            $labelChart[] = now()->subDays($i)->translatedFormat('d M');
            $cocok = $omzetMingguan->firstWhere('tanggal', $tanggal);
            $dataChart[] = $cocok ? (int) $cocok->total : 0;
        }

        $view = view('dashboard-kasir.index', compact(
            'omzetHariIni',
            'jumlahTransaksiHariIni',
            'produkTerlaris',
            'metodeBayar',
            'transaksiTerbaru',
            'labelChart',
            'dataChart'
        ));

        // Chart omzet bulanan HANYA dihitung dan dikirim untuk admin.
        // Untuk kasir, datanya tidak ikut ke browser sama sekali.
        if ($request->user()->isAdmin()) {
            $view->with($this->dataOmzetBulanan($request));
        }

        return $view;
    }

    /**
     * Omzet per bulan (Jan-Des) untuk satu tahun. Bulan tanpa transaksi bernilai 0.
     */
    private function dataOmzetBulanan(Request $request): array
    {
        // Pilihan tahun: dari tahun transaksi pertama sampai tahun berjalan (terbaru di atas)
        $tahunSekarang = now()->year;
        $transaksiPertama = Transaksi::min('created_at');
        $tahunAwal = $transaksiPertama ? Carbon::parse($transaksiPertama)->year : $tahunSekarang;
        $daftarTahun = range($tahunSekarang, min($tahunAwal, $tahunSekarang));

        // Tahun dari URL hanya dipakai kalau ada di daftar; selain itu tahun berjalan
        $tahun = (int) $request->query('tahun', $tahunSekarang);
        if (! in_array($tahun, $daftarTahun, true)) {
            $tahun = $tahunSekarang;
        }

        // SUBSTR(created_at, 1, 7) menghasilkan 'YYYY-MM' dan jalan di MySQL maupun
        // SQLite (dipakai test), jadi tidak memakai fungsi tanggal khusus salah satunya.
        $perBulan = Transaksi::selectRaw('SUBSTR(created_at, 1, 7) as bulan, SUM(total_bayar) as total')
            ->whereBetween('created_at', [
                Carbon::create($tahun, 1, 1)->startOfDay(),
                Carbon::create($tahun, 12, 31)->endOfDay(),
            ])
            ->groupBy('bulan')
            ->pluck('total', 'bulan');

        $dataBulanan = [];
        foreach (range(1, 12) as $bulan) {
            $kunci = sprintf('%04d-%02d', $tahun, $bulan);
            $dataBulanan[] = (int) ($perBulan[$kunci] ?? 0);
        }

        return [
            'labelBulanan' => self::NAMA_BULAN,
            'dataBulanan' => $dataBulanan,
            'tahunDipilih' => $tahun,
            'daftarTahun' => $daftarTahun,
            'totalOmzetTahun' => array_sum($dataBulanan),
        ];
    }
}