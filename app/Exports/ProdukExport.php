<?php

namespace App\Exports;

use App\Models\Produk;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class ProdukExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    public function collection()
    {
        return Produk::with('kategori')->get();
    }

    public function headings(): array
    {
        return ['Nama Produk', 'Kategori', 'Harga', 'Stok'];
    }

    public function map($produk): array
    {
        return [
            $produk->nama_produk,
            $produk->kategori->nama_kategori ?? '-',
            $produk->harga,
            $produk->stok,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Baris 1 (header): bold, background abu-abu, teks putih
        $sheet->getStyle('A1:D1')->getFont()->setBold(true);
        $sheet->getStyle('A1:D1')->getFont()->getColor()->setRGB('FFFFFF');
        $sheet->getStyle('A1:D1')->getFill()
            ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
            ->getStartColor()->setRGB('198754');

        // Rata tengah untuk header
        $sheet->getStyle('A1:D1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Border tipis untuk semua sel yang ada datanya
        $highestRow = $sheet->getHighestRow();
        $sheet->getStyle("A1:D{$highestRow}")->getBorders()
            ->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        return [];
    }
}