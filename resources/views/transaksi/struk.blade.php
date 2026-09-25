<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk {{ $transaksi->kode_transaksi }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Courier New', monospace;
            font-size: 12px;
            width: 280px;
            margin: 20px auto;
            color: #000;
        }

        .center {
            text-align: center;
        }

        .divider {
            border-top: 1px dashed #000;
            margin: 8px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            padding: 2px 0;
            vertical-align: top;
        }

        .text-right {
            text-align: right;
        }

        .item-name {
            font-weight: bold;
        }

        .btn-print {
            display: block;
            width: 100%;
            margin-top: 15px;
            padding: 10px;
            background: #198754;
            color: #fff;
            border: none;
            border-radius: 4px;
            font-size: 14px;
            cursor: pointer;
        }

        @media print {
            .btn-print {
                display: none;
            }

            body {
                margin: 0;
            }
        }
    </style>
</head>
<body>
    <div class="center">
        <strong>Pos Kasir</strong><br>
        {{ $transaksi->created_at->format('d/m/y H:i') }}<br>
        {{ $transaksi->kode_transaksi }}
    </div>

    <div class="divider"></div>

    <table>
        @foreach ($transaksi->details as $d)
        <tr>
            <td colspan="2" class="item-name">{{ $d->nama_produk }}</td>
        </tr>
        <tr>
            <td>{{ $d->jumlah }} x {{ number_format($d->harga, 0, ',', '.') }}</td>
            <td class="text-right">{{ number_format($d->subtotal, 0, ',', '.') }}</td>
        </tr>
        @endforeach
    </table>

    <div class="divider"></div>

    <table>
        <tr>
            <td><strong>Total</strong></td>
            <td class="text-right"><strong>{{ number_format($transaksi->total_bayar, 0, ',', '.') }}</strong></td>
        </tr>
        <tr>
            <td>{{ strtoupper($transaksi->metode_bayar) }}</td>
            <td class="text-right">{{ number_format($transaksi->bayar, 0, ',', '.') }}</td>
        </tr>
        <tr>
            <td>Kembali</td>
            <td class="text-right">{{ number_format($transaksi->kembalian, 0, ',', '.') }}</td>
        </tr>
    </table>

    <div class="divider"></div>

    <div class="center">
        Kasir: {{ $transaksi->user->name ?? '-' }}<br><br>
        Terima kasih atas kunjungan Anda!
    </div>

    <button class="btn-print" onclick="window.print()">🖨️ Cetak Struk</button>
</body>
</html>