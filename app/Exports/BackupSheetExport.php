<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Illuminate\Support\Collection;

class BackupSheetExport implements FromCollection, WithHeadings, WithTitle, WithStyles, WithColumnWidths
{
    public function __construct(
        protected $data,
        protected array $headings,
        protected string $type
    ) {}

    public function collection(): Collection
    {
        return collect($this->data)->map(function ($row) {
            $row = is_object($row) ? $row->toArray() : $row;

            return match($this->type) {
                'items' => [
                    $row['id'],
                    $row['code']                                ?? '-',
                    $row['name'],
                    $row['category']['name']                    ?? '-',
                    $row['supplier']['name']                    ?? '-',
                    $row['unit']['name']                        ?? '-',
                    $row['storage_location']['name']            ?? '-',
                    $row['stock'],
                    $row['stock_minimum']                       ?? 0,
                    $row['stock_maximum']                       ?? '-',
                    $row['purchase_price']                      ?? 0,
                    ($row['is_active'] ?? false) ? 'Aktif' : 'Nonaktif',
                    $row['created_at']                          ?? '-',
                ],
                'transactions' => [
                    $row['id'],
                    $row['code']                                ?? '-',
                    ($row['type'] ?? '') === 'in' ? 'Barang Masuk' : 'Barang Keluar',
                    $row['item']['name']                        ?? '-',
                    $row['quantity']                            ?? 0,
                    $row['price']                               ?? 0,
                    (($row['quantity'] ?? 0) * ($row['price'] ?? 0)),
                    $row['note']                                ?? '-',
                    $row['transaction_date']                    ?? '-',
                    $row['user']['name']                        ?? '-',
                ],
                'categories' => [
                    $row['id'],
                    $row['name'],
                    $row['slug']                                ?? '-',
                    $row['description']                         ?? '-',
                    ($row['is_active'] ?? false) ? 'Aktif' : 'Nonaktif',
                ],
                'suppliers' => [
                    $row['id'],
                    $row['code']                                ?? '-',
                    $row['name'],
                    $row['contact_person']                      ?? '-',
                    $row['phone']                               ?? '-',
                    $row['email']                               ?? '-',
                    $row['city']                                ?? '-',
                    ($row['is_active'] ?? false) ? 'Aktif' : 'Nonaktif',
                ],
                'units' => [
                    $row['id'],
                    $row['name'],
                    $row['abbreviation']                        ?? '-',
                ],
                'storage_locations' => [
                    $row['id'],
                    $row['code']                                ?? '-',
                    $row['name'],
                    $row['description']                         ?? '-',
                    ($row['is_active'] ?? false) ? 'Aktif' : 'Nonaktif',
                ],
                'users' => [
                    $row['id'],
                    $row['name'],
                    $row['email'],
                    $row['role'],
                    ($row['is_active'] ?? false) ? 'Aktif' : 'Nonaktif',
                    $row['created_at']                          ?? '-',
                ],
                default => [],
            };
        });
    }

    public function headings(): array
    {
        return $this->headings;
    }

    public function title(): string
    {
        return match($this->type) {
            'items'             => 'Data Barang',
            'transactions'      => 'Transaksi',
            'categories'        => 'Kategori',
            'suppliers'         => 'Supplier',
            'units'             => 'Satuan',
            'storage_locations' => 'Lokasi Penyimpanan',
            'users'             => 'User',
            default             => 'Sheet',
        };
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font'      => ['bold' => true, 'color' => ['argb' => 'FFFFFFFF']],
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['argb' => 'FF0C447C']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return match($this->type) {
            'items'             => ['A' => 5, 'B' => 12, 'C' => 20, 'D' => 15, 'E' => 20, 'F' => 10, 'G' => 15, 'H' => 8, 'I' => 8, 'J' => 8, 'K' => 12, 'L' => 10, 'M' => 20],
            'transactions'      => ['A' => 5, 'B' => 15, 'C' => 15, 'D' => 20, 'E' => 8, 'F' => 12, 'G' => 12, 'H' => 20, 'I' => 12, 'J' => 15],
            'categories'        => ['A' => 5, 'B' => 15, 'C' => 15, 'D' => 25, 'E' => 10],
            'suppliers'         => ['A' => 5, 'B' => 10, 'C' => 25, 'D' => 20, 'E' => 15, 'F' => 25, 'G' => 15, 'H' => 10],
            'units'             => ['A' => 5, 'B' => 15, 'C' => 10],
            'storage_locations' => ['A' => 5, 'B' => 10, 'C' => 15, 'D' => 25, 'E' => 10],
            'users'             => ['A' => 5, 'B' => 20, 'C' => 25, 'D' => 10, 'E' => 10, 'F' => 20],
            default             => [],
        };
    }
}