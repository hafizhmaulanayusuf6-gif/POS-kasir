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
            <div class="row">
                @forelse ($produks as $p)
                <div class="col-md-3 mb-3">
                    <div class="card h-100 produk-card" style="cursor:pointer"
                         data-id="{{ $p->id }}"
                         data-nama="{{ $p->nama_produk }}"
                         data-harga="{{ $p->harga }}"
                         data-stok="{{ $p->stok }}">
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

                        <div class="mb-2">
                            <label>Uang Bayar:</label>
                            <input type="number" name="bayar" id="input-bayar" class="form-control" min="0" required>
                        </div>

                        <div class="d-flex justify-content-between mb-3">
                            <span>Kembalian</span>
                            <span id="cart-kembalian">Rp 0</span>
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

        document.getElementById('input-bayar').addEventListener('input', function () {
            const total = cart.reduce((sum, item) => sum + (item.harga * item.jumlah), 0);
            hitungKembalian(total);
        });

        document.querySelectorAll('.produk-card').forEach(card => {
            card.addEventListener('click', function () {
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
                    cart.push({ id, nama, harga, jumlah: 1, stok });
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

        document.getElementById('form-bayar').addEventListener('submit', function (e) {
            if (cart.length === 0) {
                e.preventDefault();
                alert('Keranjang masih kosong.');
            }
        });
    </script>

@endsection