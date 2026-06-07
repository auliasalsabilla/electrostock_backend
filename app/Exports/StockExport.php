<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Illuminate\Support\Collection;

class StockExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths, WithTitle
{
    protected $data;

    public function __construct(Collection $data)
    {
        $this->data = $data;
    }

    public function collection(): Collection
    {
        return $this->data->map(function ($item, $index) {
            $perubahan = $item->total_masuk - $item->total_keluar;
            return [
                'No'          => $index + 1,
                'Nama Barang' => $item->name,
                'Kategori'    => $item->category->name ?? '-',
                'Stok Awal'   => $item->stok_awal,
                'Masuk'       => $item->total_masuk,
                'Tgl Masuk'   => $item->tgl_masuk
                    ? \Carbon\Carbon::parse($item->tgl_masuk)->format('d/m/Y')
                    : '-',
                'Keluar'      => $item->total_keluar,
                'Tgl Keluar'  => $item->tgl_keluar
                    ? \Carbon\Carbon::parse($item->tgl_keluar)->format('d/m/Y')
                    : '-',
                'Stok Akhir'  => $item->stock,
                'Perubahan'   => ($perubahan >= 0 ? '+' : '') . $perubahan,
            ];
        });
    }

    public function headings(): array
    {
        return [
            'No', 'Nama Barang', 'Kategori', 'Stok Awal',
            'Masuk', 'Tgl Masuk', 'Keluar', 'Tgl Keluar',
            'Stok Akhir', 'Perubahan',
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF0C447C']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,  'B' => 25, 'C' => 15, 'D' => 10,
            'E' => 8,  'F' => 12, 'G' => 8,  'H' => 12,
            'I' => 10, 'J' => 10,
        ];
    }

    public function title(): string
    {
        return 'Laporan Stok';
    }
}