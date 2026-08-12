<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Struk {{ $transaksi->kode_transaksi }} - Vault</title>
    <style>
        body {
            font-family: 'Courier New', Courier, monospace;
            background-color: #f1f5f9;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
        }

        .receipt-card {
            width: 320px;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            color: #000;
        }

        .receipt-header {
            text-align: center;
            border-bottom: 2px dashed #000;
            padding-bottom: 12px;
            margin-bottom: 12px;
        }

        .receipt-title {
            font-size: 20px;
            font-weight: bold;
            letter-spacing: 2px;
        }

        .receipt-sub {
            font-size: 11px;
            margin-top: 2px;
        }

        .receipt-table {
            width: 100%;
            font-size: 12px;
            border-collapse: collapse;
        }

        .receipt-table td {
            padding: 4px 0;
            vertical-align: top;
        }

        .receipt-divider {
            border-top: 1px dashed #000;
            margin: 10px 0;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .font-bold {
            font-weight: bold;
        }

        .code-box {
            border: 2px solid #000;
            padding: 8px;
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            letter-spacing: 2px;
            margin: 10px 0;
        }

        .no-print {
            text-align: center;
            margin-bottom: 15px;
        }

        .btn-print {
            background-color: #2563eb;
            color: #fff;
            border: none;
            padding: 8px 20px;
            border-radius: 20px;
            font-weight: bold;
            cursor: pointer;
            font-family: sans-serif;
        }

        @media print {
            body {
                background-color: #fff;
                padding: 0;
            }
            .receipt-card {
                box-shadow: none;
                width: 100%;
                padding: 0;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>
    <div>
        <div class="no-print">
            <button class="btn-print" onclick="window.print()">Cetak Struk Sekarang</button>
        </div>

        <div class="receipt-card">
            <div class="receipt-header">
                <img src="{{ asset('images/logo.png') }}" alt="Vault Logo" style="height: 38px; width: auto; margin-bottom: 6px;">
                <div class="receipt-title">VAULT</div>
                <div class="receipt-sub">LAYANAN PENITIPAN HELM SECURE</div>
                <div class="receipt-sub">Jl. Utama No. 88 | CS: 0812-3456-7890</div>
            </div>

            <div class="code-box">
                {{ $transaksi->kode_transaksi }}
            </div>

            <table class="receipt-table">
                <tr>
                    <td>No. Loker</td>
                    <td class="text-right font-bold" style="font-size: 16px;">LOKER {{ $transaksi->loker->nomor_loker }}</td>
                </tr>
                <tr>
                    <td>Tanggal/Jam Titip</td>
                    <td class="text-right">{{ $transaksi->tgl_titip->format('d/m/Y H:i') }}</td>
                </tr>
                <tr>
                    <td>Petugas</td>
                    <td class="text-right">{{ $transaksi->user->name }}</td>
                </tr>
            </table>

            <div class="receipt-divider"></div>

            <table class="receipt-table">
                <tr>
                    <td>Pelanggan</td>
                    <td class="text-right font-bold">{{ $transaksi->pelanggan->nama }}</td>
                </tr>
                <tr>
                    <td>No. HP</td>
                    <td class="text-right">{{ $transaksi->pelanggan->no_hp }}</td>
                </tr>
                <tr>
                    <td>Merk Helm</td>
                    <td class="text-right font-bold">{{ $transaksi->helm->merk }}</td>
                </tr>
                <tr>
                    <td>Warna Helm</td>
                    <td class="text-right">{{ $transaksi->helm->warna }}</td>
                </tr>
            </table>

            <div class="receipt-divider"></div>

            <table class="receipt-table">
                @if($transaksi->status === 'ambil')
                    <tr>
                        <td>Waktu Ambil</td>
                        <td class="text-right">{{ $transaksi->tgl_ambil->format('d/m/Y H:i') }}</td>
                    </tr>
                    <tr>
                        <td>Metode Bayar</td>
                        <td class="text-right font-bold text-uppercase">{{ $transaksi->pembayaran->metode_bayar ?? 'TUNAI' }}</td>
                    </tr>
                    <tr>
                        <td class="font-bold">TOTAL DIBAYAR</td>
                        <td class="text-right font-bold" style="font-size: 16px;">Rp {{ number_format($transaksi->tarif, 0, ',', '.') }}</td>
                    </tr>
                    <tr>
                        <td>Status Pembayaran</td>
                        <td class="text-right font-bold">LUNAS</td>
                    </tr>
                @else
                    <tr>
                        <td>Tarif Layanan</td>
                        <td class="text-right">Rp {{ number_format($transaksi->tarif ?? 2000, 0, ',', '.') }} / jam</td>
                    </tr>
                    <tr>
                        <td>Status Titipan</td>
                        <td class="text-right font-bold">AKTIF DITITIPKAN</td>
                    </tr>
                @endif
            </table>

            <div class="receipt-divider"></div>

            <div class="receipt-sub text-center">
                * Simpan struk ini untuk verifikasi saat pengambilan helm.<br>
                Terima kasih telah menggunakan Vault!
            </div>
        </div>
    </div>
</body>
</html>
