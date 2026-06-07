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

class TransactionExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths, WithTitle
{
    protected $data;
    protected $tab;

    public function __construct(Collection $data, string $tab = 'masuk')
    {
        $this->data = $data;
        $this->tab  = $tab;
    }

    public function collection(): Collection
    {
        if ($this->tab === 'masuk') {
            return $this->data->map(function ($trx, $index) {
                return [
                    'No'          => $index + 1,
                    'Tanggal'     => \Carbon\Carbon::parse($trx->transaction_date)->format('d/m/Y'),
                    'Barang'      => $trx->item->name          ?? '-',
                    'Kategori'    => $trx->item->category->name ?? '-',
                    'Jumlah'      => $trx->quantity,
                    'Harga'       => $trx->price ?? 0,
                    'Supplier'    => $trx->item->supplier->name ?? '-',
                    'Total Nilai' => ($trx->quantity * ($trx->price ?? 0)),
                ];
            });
        }

        // tab keluar
        return $this->data->map(function ($trx, $index) {
            return [
                'No'       => $index + 1,
                'Tanggal'  => \Carbon\Carbon::parse($trx->transaction_date)->format('d/m/Y'),
                'Barang'   => $trx->item->name          ?? '-',
                'Kategori' => $trx->item->category->name ?? '-',
                'Jumlah'   => $trx->quantity,
                'Tujuan'   => $trx->note      ?? '-',
                'PIC'      => $trx->user->name ?? '-',
            ];
        });
    }

    public function headings(): array
    {
        if ($this->tab === 'masuk') {
            return ['No', 'Tanggal', 'Barang', 'Kategori', 'Jumlah', 'Harga', 'Supplier', 'Total Nilai'];
        }
        return ['No', 'Tanggal', 'Barang', 'Kategori', 'Jumlah', 'Tujuan', 'PIC'];
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
            'A' => 5, 'B' => 12, 'C' => 25, 'D' => 15,
            'E' => 8, 'F' => 15, 'G' => 15, 'H' => 15,
        ];
    }

    public function title(): string
    {
        return $this->tab === 'masuk' ? 'Barang Masuk' : 'Barang Keluar';
    }
}