<?php

namespace App\Exports;

use App\Models\Transaksi;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class TransaksiExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $tanggalAwal;
    protected $tanggalAkhir;

    public function __construct($tanggalAwal = null, $tanggalAkhir = null)
    {
        $this->tanggalAwal = $tanggalAwal;
        $this->tanggalAkhir = $tanggalAkhir;
    }

    public function collection()
    {
        return Transaksi::with('user')
            ->when($this->tanggalAwal, function ($query) {
                $query->whereDate('created_at', '>=', $this->tanggalAwal);
            })
            ->when($this->tanggalAkhir, function ($query) {
                $query->whereDate('created_at', '<=', $this->tanggalAkhir);
            })
            ->latest()
            ->get();
    }

    public function headings(): array
    {
        return ['Kode Transaksi', 'Tanggal', 'Kasir', 'Metode Bayar', 'Total', 'Bayar', 'Kembalian'];
    }

    public function map($transaksi): array
    {
        return [
            $transaksi->kode_transaksi,
            $transaksi->created_at->format('d-m-Y H:i'),
            $transaksi->user->name ?? '-',
            strtoupper($transaksi->metode_bayar),
            $transaksi->total_bayar,
            $transaksi->bayar,
            $transaksi->kembalian,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:G1')->getFont()->setBold(true);
        $sheet->getStyle('A1:G1')->getFont()->getColor()->setRGB('FFFFFF');
        $sheet->getStyle('A1:G1')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setRGB('198754');

        $sheet->getStyle('A1:G1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        $highestRow = $sheet->getHighestRow();
        $sheet->getStyle("A1:G{$highestRow}")->getBorders()
            ->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        return [];
    }
}