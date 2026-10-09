<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaksi;
use App\Exports\TransaksiExport;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

class TransaksiController extends Controller
{
    /**
     * Menentukan rentang tanggal berdasarkan filter yang dipilih.
     */
    private function getRentangTanggal(Request $request): array
    {
        // Tetap mendukung URL lama yang menggunakan parameter tanggal.
        $periode = $request->input('periode');

        if (!$periode) {
            $periode = $request->filled('tanggal')
                ? 'harian'
                : 'semua';
        }

        $request->merge(['periode' => $periode]);

        $request->validate([
            'periode' => 'in:semua,harian,mingguan,bulanan,rentang',
            'tanggal' => 'nullable|date',
            'tanggal_awal' => 'nullable|required_if:periode,rentang|date',
            'tanggal_akhir' => 'nullable|required_if:periode,rentang|date|after_or_equal:tanggal_awal',
        ]);

        if ($periode === 'semua') {
            return [null, null];
        }

        if ($periode === 'rentang') {
            return [
                $request->tanggal_awal,
                $request->tanggal_akhir,
            ];
        }

        $tanggal = Carbon::parse(
            $request->input('tanggal', now()->toDateString())
        );

        switch ($periode) {
            case 'mingguan':
                $awal = $tanggal->copy()->startOfWeek(Carbon::MONDAY);
                $akhir = $tanggal->copy()->endOfWeek(Carbon::SUNDAY);
                break;

            case 'bulanan':
                $awal = $tanggal->copy()->startOfMonth();
                $akhir = $tanggal->copy()->endOfMonth();
                break;

            default:
                $awal = $tanggal->copy();
                $akhir = $tanggal->copy();
                break;
        }

        return [
            $awal->toDateString(),
            $akhir->toDateString(),
        ];
    }

    public function index(Request $request)
    {
        [$tanggalAwal, $tanggalAkhir] =
            $this->getRentangTanggal($request);

        $transaksis = Transaksi::with('user')
            ->when($tanggalAwal && $tanggalAkhir, function ($query) use (
                $tanggalAwal,
                $tanggalAkhir
            ) {
                $query->whereBetween('created_at', [
                    Carbon::parse($tanggalAwal)->startOfDay(),
                    Carbon::parse($tanggalAkhir)->endOfDay(),
                ]);
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('transaksi.index', compact('transaksis'));
    }

    public function show(Transaksi $transaksi)
    {
        $transaksi->load('details', 'user');

        return view('transaksi.show', compact('transaksi'));
    }

    public function struk(Transaksi $transaksi)
    {
        $transaksi->load('details', 'user');

        return view('transaksi.struk', compact('transaksi'));
    }

    public function export(Request $request)
    {
        [$tanggalAwal, $tanggalAkhir] =
            $this->getRentangTanggal($request);

        $nama = 'laporan-transaksi-' . now()->format('Ymd') . '.xlsx';

        return Excel::download(
            new TransaksiExport($tanggalAwal, $tanggalAkhir),
            $nama
        );
    }
}