<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 10px; }
        h2   { color: #0C447C; margin-bottom: 5px; }
        p    { margin: 0 0 10px; color: #555; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th { background: #0C447C; color: white; padding: 6px 8px; text-align: left; }
        td { padding: 5px 8px; border-bottom: 1px solid #eee; }
        tr:nth-child(even) { background: #f5f5f5; }
    </style>
</head>
<body>
    <h2>{{ $title }}</h2>
    <p>Dicetak: {{ now()->format('d/m/Y H:i') }}</p>
    <p>Total Data: {{ count($data) }}</p>

    @if($tab === 'stok')
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Barang</th>
                <th>Kategori</th>
                <th>Stok Awal</th>
                <th>Masuk</th>
                <th>Tgl Masuk</th>
                <th>Keluar</th>
                <th>Tgl Keluar</th>
                <th>Stok Akhir</th>
                <th>Perubahan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $i => $item)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $item->name }}</td>
                <td>{{ $item->category->name ?? '-' }}</td>
                <td>{{ $item->stok_awal }}</td>
                <td>+{{ $item->total_masuk }}</td>
                <td>{{ $item->tgl_masuk ? \Carbon\Carbon::parse($item->tgl_masuk)->format('d/m/Y') : '-' }}</td>
                <td>-{{ $item->total_keluar }}</td>
                <td>{{ $item->tgl_keluar ? \Carbon\Carbon::parse($item->tgl_keluar)->format('d/m/Y') : '-' }}</td>
                <td><strong>{{ $item->stock }}</strong></td>
                <td>{{ ($item->total_masuk - $item->total_keluar) >= 0 ? '+' : '' }}{{ $item->total_masuk - $item->total_keluar }}</td>
            </tr>
            @empty
            <tr><td colspan="10" style="text-align:center">Tidak ada data</td></tr>
            @endforelse
        </tbody>
    </table>

    @elseif($tab === 'masuk')
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Barang</th>
                <th>Kategori</th>
                <th>Jumlah</th>
                <th>Harga Satuan</th>
                <th>Supplier</th>
                <th>Total Nilai</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $i => $item)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($item->transaction_date)->format('d/m/Y') }}</td>
                <td>{{ $item->item->name ?? '-' }}</td>
                <td>{{ $item->item->category->name ?? '-' }}</td>
                <td>+{{ $item->quantity }} Unit</td>
                <td>{{ $item->price ? 'Rp ' . number_format($item->price, 0, ',', '.') : '-' }}</td>
                <td>{{ $item->item->supplier->name ?? '-' }}</td>
                <td>{{ $item->price ? 'Rp ' . number_format($item->price * $item->quantity, 0, ',', '.') : '-' }}</td>
            </tr>
            @empty
            <tr><td colspan="8" style="text-align:center">Tidak ada data</td></tr>
            @endforelse
        </tbody>
    </table>

    @else
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Tanggal</th>
                <th>Barang</th>
                <th>Kategori</th>
                <th>Jumlah</th>
                <th>Tujuan</th>
                <th>PIC</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $i => $item)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($item->transaction_date)->format('d/m/Y') }}</td>
                <td>{{ $item->item->name ?? '-' }}</td>
                <td>{{ $item->item->category->name ?? '-' }}</td>
                <td>-{{ $item->quantity }} Unit</td>
                <td>{{ $item->note ?? '-' }}</td>
                <td>{{ $item->user->name ?? '-' }}</td>
            </tr>
            @empty
            <tr><td colspan="7" style="text-align:center">Tidak ada data</td></tr>
            @endforelse
        </tbody>
    </table>
    @endif
</body>
</html>