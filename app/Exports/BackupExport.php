<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class BackupExport implements WithMultipleSheets
{
    public function __construct(protected array $data) {}

    public function sheets(): array
    {
        return [
            new BackupSheetExport($this->data['items'],              $this->itemsHeadings(),    'items'),
            new BackupSheetExport($this->data['transactions'],        $this->transactionsHeadings(), 'transactions'),
            new BackupSheetExport($this->data['categories'],          $this->categoriesHeadings(),   'categories'),
            new BackupSheetExport($this->data['suppliers'],           $this->suppliersHeadings(),    'suppliers'),
            new BackupSheetExport($this->data['units'],               $this->unitsHeadings(),        'units'),
            new BackupSheetExport($this->data['storage_locations'],   $this->storageHeadings(),      'storage_locations'),
            new BackupSheetExport($this->data['users'],               $this->usersHeadings(),        'users'),
        ];
    }

    private function itemsHeadings(): array
    {
        return ['ID', 'Kode', 'Nama', 'Kategori', 'Supplier', 'Satuan', 'Lokasi', 'Stok', 'Stok Min', 'Stok Max', 'Harga Beli', 'Status', 'Dibuat'];
    }

    private function transactionsHeadings(): array
    {
        return ['ID', 'Kode', 'Jenis', 'Barang', 'Jumlah', 'Harga', 'Total', 'Catatan', 'Tanggal', 'User'];
    }

    private function categoriesHeadings(): array
    {
        return ['ID', 'Nama', 'Slug', 'Deskripsi', 'Status'];
    }

    private function suppliersHeadings(): array
    {
        return ['ID', 'Kode', 'Nama', 'Contact Person', 'Telepon', 'Email', 'Kota', 'Status'];
    }

    private function unitsHeadings(): array
    {
        return ['ID', 'Nama', 'Singkatan'];
    }

    private function storageHeadings(): array
    {
        return ['ID', 'Kode', 'Nama', 'Deskripsi', 'Status'];
    }

    private function usersHeadings(): array
    {
        return ['ID', 'Nama', 'Email', 'Role', 'Status', 'Dibuat'];
    }
}