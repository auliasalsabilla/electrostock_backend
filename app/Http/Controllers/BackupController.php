<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Supplier;
use App\Models\Category;
use App\Models\Unit;
use App\Models\StorageLocation;
use App\Models\User;
use App\Models\Transaction;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BackupExport;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use PhpOffice\PhpSpreadsheet\IOFactory;

class BackupController extends Controller
{
    public function backup(Request $request)
    {
        $data = [
            'categories'        => Category::select('id', 'name', 'slug', 'description', 'is_active')->get(),
            'suppliers'         => Supplier::select('id', 'code', 'name', 'contact_person', 'phone', 'email', 'city', 'is_active')->get(),
            'units'             => Unit::select('id', 'name', 'abbreviation')->get(),
            'storage_locations' => StorageLocation::select('id', 'code', 'name', 'description', 'is_active')->get(),
            'items'             => Item::with([
                                    'category:id,name',
                                    'unit:id,name',
                                    'supplier:id,name',
                                    'storageLocation:id,name',
                                  ])
                                  ->select('id', 'code', 'name', 'category_id', 'supplier_id', 'unit_id', 'storage_location_id', 'stock', 'stock_minimum', 'stock_maximum', 'purchase_price', 'is_active', 'created_at')
                                  ->get(),
            'transactions'      => Transaction::with([
                                    'item:id,name,code',
                                    'user:id,name',
                                  ])
                                  ->select('id', 'code', 'type', 'item_id', 'user_id', 'quantity', 'price', 'note', 'transaction_date')
                                  ->orderBy('transaction_date', 'desc')
                                  ->limit(1000)
                                  ->get(),
            'users'             => User::select('id', 'name', 'email', 'role', 'is_active', 'created_at')->get(),
        ];

        $filename = 'backup_' . now()->format('Y_m_d_His') . '.xlsx';

        return Excel::download(new BackupExport($data), $filename);
    }

    public function restore(Request $request): JsonResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:xlsx,xls', 'max:10240'],
        ]);

        try {
            DB::beginTransaction();

            $file        = $request->file('file');
            $spreadsheet = IOFactory::load($file->getPathname());
            $restored    = [];

            // === RESTORE CATEGORIES ===
            $sheet = $spreadsheet->getSheetByName('Kategori');
            if ($sheet) {
                $rows = $sheet->toArray();
                array_shift($rows); // hapus header
                foreach ($rows as $row) {
                    if (empty($row[0]) || empty($row[1])) continue;
                    Category::updateOrCreate(
                        ['id' => $row[0]],
                        [
                            'name'        => $row[1] ?? '',
                            'slug'        => $row[2] ?? \Illuminate\Support\Str::slug($row[1]),
                            'description' => $row[3] ?? null,
                            'is_active'   => ($row[4] ?? 'Aktif') === 'Aktif',
                        ]
                    );
                }
                $restored[] = 'Kategori';
            }

            // === RESTORE SUPPLIERS ===
            $sheet = $spreadsheet->getSheetByName('Supplier');
            if ($sheet) {
                $rows = $sheet->toArray();
                array_shift($rows);
                foreach ($rows as $row) {
                    if (empty($row[0]) || empty($row[2])) continue;
                    Supplier::updateOrCreate(
                        ['id' => $row[0]],
                        [
                            'code'           => $row[1] ?? '',
                            'name'           => $row[2] ?? '',
                            'contact_person' => $row[3] ?? null,
                            'phone'          => $row[4] ?? null,
                            'email'          => $row[5] ?? null,
                            'city'           => $row[6] ?? null,
                            'is_active'      => ($row[7] ?? 'Aktif') === 'Aktif',
                        ]
                    );
                }
                $restored[] = 'Supplier';
            }

            // === RESTORE UNITS ===
            $sheet = $spreadsheet->getSheetByName('Satuan');
            if ($sheet) {
                $rows = $sheet->toArray();
                array_shift($rows);
                foreach ($rows as $row) {
                    if (empty($row[0]) || empty($row[1])) continue;
                    Unit::updateOrCreate(
                        ['id' => $row[0]],
                        [
                            'name'         => $row[1] ?? '',
                            'abbreviation' => $row[2] ?? '',
                        ]
                    );
                }
                $restored[] = 'Satuan';
            }

            // === RESTORE STORAGE LOCATIONS ===
            $sheet = $spreadsheet->getSheetByName('Lokasi Penyimpanan');
            if ($sheet) {
                $rows = $sheet->toArray();
                array_shift($rows);
                foreach ($rows as $row) {
                    if (empty($row[0]) || empty($row[2])) continue;
                    StorageLocation::updateOrCreate(
                        ['id' => $row[0]],
                        [
                            'code'        => $row[1] ?? '',
                            'name'        => $row[2] ?? '',
                            'description' => $row[3] ?? null,
                            'is_active'   => ($row[4] ?? 'Aktif') === 'Aktif',
                        ]
                    );
                }
                $restored[] = 'Lokasi Penyimpanan';
            }

            // === RESTORE ITEMS ===
            $sheet = $spreadsheet->getSheetByName('Data Barang');
            if ($sheet) {
                $rows = $sheet->toArray();
                array_shift($rows);
                foreach ($rows as $row) {
                    if (empty($row[0]) || empty($row[2])) continue;

                    $category = Category::where('name', $row[3])->first();
                    $supplier = Supplier::where('name', $row[4])->first();
                    $unit     = Unit::where('name', $row[5])->first();
                    $location = StorageLocation::where('name', $row[6])->first();

                    Item::updateOrCreate(
                        ['id' => $row[0]],
                        [
                            'code'                => $row[1] ?? '',
                            'name'                => $row[2] ?? '',
                            'category_id'         => $category?->id,
                            'supplier_id'         => $supplier?->id,
                            'unit_id'             => $unit?->id,
                            'storage_location_id' => $location?->id,
                            'stock'               => $row[7] ?? 0,
                            'stock_minimum'       => $row[8] ?? 0,
                            'stock_maximum'       => $row[9] !== '-' ? $row[9] : null,
                            'purchase_price'      => $row[10] ?? 0,
                            'is_active'           => ($row[11] ?? 'Aktif') === 'Aktif',
                        ]
                    );
                }
                $restored[] = 'Data Barang';
            }

            // === RESTORE USERS ===
            $sheet = $spreadsheet->getSheetByName('User');
            if ($sheet) {
                $rows = $sheet->toArray();
                array_shift($rows);
                foreach ($rows as $row) {
                    if (empty($row[0]) || empty($row[2])) continue;
                    $existing = User::find($row[0]);
                    User::updateOrCreate(
                        ['id' => $row[0]],
                        [
                            'name'      => $row[1] ?? '',
                            'email'     => $row[2] ?? '',
                            'role'      => $row[3] ?? 'staff',
                            'is_active' => ($row[4] ?? 'Aktif') === 'Aktif',
                            'password'  => $existing?->password ?? Hash::make('password123'),
                        ]
                    );
                }
                $restored[] = 'User';
            }

            DB::commit();

            return response()->json([
                'status'   => true,
                'message'  => 'Restore berhasil! Data yang dipulihkan: ' . implode(', ', $restored),
                'restored' => $restored,
            ]);

        } catch (\Exception $e) {
    DB::rollBack();
    \Illuminate\Support\Facades\Log::error('Restore error: ' . $e->getMessage() . ' | Line: ' . $e->getLine() . ' | File: ' . $e->getFile());
    return response()->json([
        'status'  => false,
        'message' => 'Restore gagal: ' . $e->getMessage(),
    ], 500);
}
    }
}