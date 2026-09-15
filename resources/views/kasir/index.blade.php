@extends('layouts.produk')

@section('title', 'Kasir')

@section('content')

<h2>Kasir</h2>

@if (session('error'))
<div class="alert alert-danger">{{ session('error') }}</div>
@endif
@if (session('success'))
<div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="row">
    <div class="col-md-8">
        <!-- Search Produk -->
        <div class="mb-3">
            <input type="text"
                id="search-produk"
                class="form-control"
                placeholder="Cari nama produk...">
        </div>

        <!-- Filtet kategori -->
        <div class="mb-3 d-flex flex-wrap gap-2" id="filter-kategori">
            <button type="button" class="btn btn-sm btn-primary filter-btn" data-kategori="semua">
                Semua
            </button>
            @foreach ($kategoris as $kategori)
            <button type="button" class="btn btn-sm btn-outline-primary filter-btn" data-kategori="{{ $kategori->id}}">
                {{ $kategori->nama_kategori }}
            </button>
            @endforeach
        </div>

        <div class="row">
            @forelse ($produks as $p)
            <div class="col-md-3 mb-3 produk-col" data-kategori="{{ $p->kategori_id ?? 'tanpa-kategori' }}">
                <div class="card h-100 produk-card" style="cursor:pointer"
                    data-id="{{ $p->id }}"
                    data-nama="{{ $p->nama_produk }}"
                    data-harga="{{ $p->harga }}"
                    data-stok="{{ $p->stok }}"
                    data-kategori="{{ $p->kategori_id ?? 'tanpa-kategori' }}">
                    @if ($p->gambar)
                    <img src="{{ asset('storage/' . $p->gambar) }}" class="card-img-top" style="height:120px;object-fit:cover;">
                    @else
                    <div class="bg-light d-flex align-items-center justify-content-center" style="height:120px;">
                        <span class="text-muted">Tanpa gambar</span>
                    </div>
                    @endif
                    <div class="card-body">
                        <h6 class="card-title mb-1">{{ $p->nama_produk }}</h6>
                        <div class="text-muted small">Stok: {{ $p->stok }}</div>
                        <div class="fw-bold">Rp {{ number_format($p->harga, 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>
            @empty
            <p class="text-muted">Tidak ada produk dengan stok tersedia.</p>
            @endforelse
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">Keranjang</div>
            <ul class="list-group list-group-flush" id="cart-list">
                <li class="list-group-item text-muted" id="cart-empty">Keranjang masih kosong.</li>
            </ul>
            <div class="card-body">
                <div class="d-flex justify-content-between mb-2">
                    <strong>Total</strong>
                    <strong id="cart-total">Rp 0</strong>
                </div>

                <form action="{{ route('kasir.store') }}" method="POST" id="form-bayar">
                    @csrf
                    <input type="hidden" name="cart" id="cart-input">
                    <input type="hidden" name="metode_bayar" id="metode-bayar-input" value="cash">

                    <div class="mb-3">
                        <label class="form-label">Metode Pembayaran:</label>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-sm btn-primary metode-btn" data-metode="cash">Cash</button>
                            <button type="button" class="btn btn-sm btn-outline-primary metode-btn" data-metode="qris">QRIS</button>
                            <button type="button" class="btn btn-sm btn-outline-primary metode-btn" data-metode="transfer">Transfer</button>
                        </div>
                    </div>

                    <div id="area-cash">
                        <div class="mb-2">
                            <label>Uang Bayar:</label>
                            <input type="number" name="bayar" id="input-bayar" class="form-control" min="0" required>
                        </div>

                        <div class="d-flex justify-content-between mb-3">
                            <span>Kembalian</span>
                            <span id="cart-kembalian">Rp 0</span>
                        </div>
                    </div>

                    <div id="area-nontunai" class="d-none">
                        <div class="alert alert-info small mb-3">
                            Pembayaran <strong id="label-metode"></strong> sejumlah <strong id="label-total-nontunai">Rp 0</strong> akan dianggap lunas.
                        </div>
                    </div>

                    <button type="submit" class="btn btn-success w-100">Bayar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    let cart = [];

    function renderCart() {
        const list = document.getElementById('cart-list');
        list.innerHTML = '';

        if (cart.length === 0) {
            list.innerHTML = '<li class="list-group-item text-muted">Keranjang masih kosong.</li>';
        } else {
            cart.forEach((item, index) => {
                const li = document.createElement('li');
                li.className = 'list-group-item d-flex justify-content-between align-items-center';
                li.innerHTML = `
                        <div>
                            <div>${item.nama}</div>
                            <small class="text-muted">Rp ${item.harga.toLocaleString('id-ID')} x ${item.jumlah}</small>
                        </div>
                        <div>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="ubahJumlah(${index}, -1)">-</button>
                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="ubahJumlah(${index}, 1)">+</button>
                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="hapusItem(${index})">x</button>
                        </div>
                    `;
                list.appendChild(li);
            });
        }

        hitungTotal();
    }

    function hitungTotal() {
        const total = cart.reduce((sum, item) => sum + (item.harga * item.jumlah), 0);
        document.getElementById('cart-total').innerText = 'Rp ' + total.toLocaleString('id-ID');
        document.getElementById('cart-input').value = JSON.stringify(cart);
        hitungKembalian(total);
    }

    function hitungKembalian(total) {
        const bayar = parseInt(document.getElementById('input-bayar').value) || 0;
        const kembalian = bayar - total;
        document.getElementById('cart-kembalian').innerText = 'Rp ' + (kembalian > 0 ? kembalian.toLocaleString('id-ID') : 0);
    }

    document.getElementById('input-bayar').addEventListener('input', function() {
        const total = cart.reduce((sum, item) => sum + (item.harga * item.jumlah), 0);
        hitungKembalian(total);
    });

    document.querySelectorAll('.produk-card').forEach(card => {
        card.addEventListener('click', function() {
            const id = parseInt(this.dataset.id);
            const nama = this.dataset.nama;
            const harga = parseInt(this.dataset.harga);
            const stok = parseInt(this.dataset.stok);

            const existing = cart.find(item => item.id === id);

            if (existing) {
                if (existing.jumlah < stok) {
                    existing.jumlah++;
                } else {
                    alert('Stok tidak mencukupi.');
                    return;
                }
            } else {
                cart.push({
                    id,
                    nama,
                    harga,
                    jumlah: 1,
                    stok
                });
            }

            renderCart();
        });
    });

    function ubahJumlah(index, delta) {
        cart[index].jumlah += delta;
        if (cart[index].jumlah <= 0) {
            cart.splice(index, 1);
        } else if (cart[index].jumlah > cart[index].stok) {
            cart[index].jumlah = cart[index].stok;
            alert('Stok tidak mencukupi.');
        }
        renderCart();
    }

    function hapusItem(index) {
        cart.splice(index, 1);
        renderCart();
    }

    document.getElementById('form-bayar').addEventListener('submit', function(e) {
        if (cart.length === 0) {
            e.preventDefault();
            alert('Keranjang masih kosong.');
        }
    });

    // Filter kategori
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.addEventListener('click', function() {

            // Reset tampilan tombol aktif
            document.querySelectorAll('.filter-btn').forEach(b => {
                b.classList.remove('btn-primary');
                b.classList.add('btn-outline-primary');
            });

            // Aktifkan tombol yang diklik
            this.classList.remove('btn-outline-primary');
            this.classList.add('btn-primary');

            // Simpan kategori yang dipilih
            const kategoriId = this.dataset.kategori;

            // Simpan kategori yang sedang aktif
            window.kategoriAktif = kategoriId;

            filterProduk();
        });
    });


    // Search produk
    document.getElementById('search-produk').addEventListener('input', function() {

        // Ambil teks yang diketik
        const keyword = this.value.toLowerCase().trim();

        // Simpan keyword search
        window.keywordSearch = keyword;

        filterProduk();
    });


    // Fungsi untuk search + filter kategori
    function filterProduk() {

        const keyword = window.keywordSearch || '';
        const kategoriId = window.kategoriAktif || 'semua';

        document.querySelectorAll('.produk-col').forEach(col => {

            // Ambil nama produk
            const namaProduk = col.querySelector('.produk-card').dataset.nama.toLowerCase();

            // Cek apakah nama produk cocok dengan search
            const cocokSearch = namaProduk.includes(keyword);

            // Cek apakah kategori cocok
            const cocokKategori =
                kategoriId === 'semua' ||
                col.dataset.kategori === kategoriId;

            // Produk hanya ditampilkan jika kedua kondisi terpenuhi
            if (cocokSearch && cocokKategori) {
                col.style.display = '';
            } else {
                col.style.display = 'none';
            }
        });
    }

    // Toggle metode pembayaran
    document.querySelectorAll('.metode-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.metode-btn').forEach(b => {
                b.classList.remove('btn-primary');
                b.classList.add('btn-outline-primary');
            });
            this.classList.remove('btn-outline-primary');
            this.classList.add('btn-primary');

            const metode = this.dataset.metode;
            document.getElementById('metode-bayar-input').value = metode;

            const areaCash = document.getElementById('area-cash');
            const areaNontunai = document.getElementById('area-nontunai');
            const inputBayar = document.getElementById('input-bayar');

            if (metode === 'cash') {
                areaCash.classList.remove('d-none');
                areaNontunai.classList.add('d-none');
                inputBayar.setAttribute('required', 'required');
            } else {
                areaCash.classList.add('d-none');
                areaNontunai.classList.remove('d-none');
                inputBayar.removeAttribute('required');

                const total = cart.reduce((sum, item) => sum + (item.harga * item.jumlah), 0);
                document.getElementById('label-metode').innerText = metode.toUpperCase();
                document.getElementById('label-total-nontunai').innerText = 'Rp ' + total.toLocaleString('id-ID');
            }
        });
    });
</script>

@endsection