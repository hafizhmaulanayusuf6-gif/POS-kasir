<?php

namespace App\Imports;

use App\Models\Produk;
use App\Models\Kategori;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ProdukImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        $kategori = Kategori::where('nama_kategori', $row['kategori'])->first();

        return new Produk([
            'nama_produk' => $row['nama_produk'],
            'kategori_id' => $kategori->id ?? null,
            'harga' => $row['harga'],
            'stok' => $row['stok'],
        ]);
    }

    public function rules(): array
    {
        return [
            'nama_produk' => 'required|string|max:255',
            'harga' => 'required|numeric|min:0',
            'stok' => 'required|integer|min:0',
        ];
    }

    public function headingRow(): int
    {
        return 1;
    }
}