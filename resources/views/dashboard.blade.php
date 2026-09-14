<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm text-gray-500">Total Produk</div>
                    <div class="text-3xl font-bold text-gray-800">{{ $totalProduk }}</div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm text-gray-500">Total Kategori</div>
                    <div class="text-3xl font-bold text-gray-800">{{ $totalKategori }}</div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm text-gray-500">Total Stok (unit)</div>
                    <div class="text-3xl font-bold text-gray-800">{{ $totalStok }}</div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm text-gray-500">Total Nilai Stok</div>
                    <div class="text-2xl font-bold text-gray-800">Rp {{ number_format($totalNilaiStok, 0, ',', '.') }}</div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="text-sm text-gray-500">Produk Stok Menipis (&lt;5)</div>
                    <div class="text-3xl font-bold {{ $produkStokMenipis > 0 ? 'text-red-600' : 'text-gray-800' }}">
                        {{ $produkStokMenipis }}
                    </div>
                </div>

            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Produk per Kategori</h3>
                <table class="w-full text-left">
                    <thead>
                        <tr class="border-b">
                            <th class="pb-2">Kategori</th>
                            <th class="pb-2">Jumlah Produk</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($produkPerKategori as $kategori)
                        <tr class="border-b">
                            <td class="py-2">{{ $kategori->nama_kategori }}</td>
                            <td class="py-2">{{ $kategori->produks_count }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2" class="py-2 text-gray-500">Belum ada kategori.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                <a href="{{ route('produk.index') }}" class="text-blue-600 hover:underline">→ Lihat Daftar Produk</a>
            </div>

        </div>
    </div>
</x-app-layout>