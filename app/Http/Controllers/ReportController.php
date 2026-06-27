<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\StockExport;
use App\Exports\TransactionExport;

class ReportController extends Controller
{
    public function stock(): JsonResponse
    {
        $items = Cache::remember('report_stock', 60, function () {

            // Ambil semua transaksi masuk & keluar sekaligus (1 query)
            $transaksiMasuk = Transaction::where('type', 'in')
                ->selectRaw('item_id, SUM(quantity) as total, MAX(transaction_date) as tgl')
                ->groupBy('item_id')
                ->get()
                ->keyBy('item_id');

            $transaksiKeluar = Transaction::where('type', 'out')
                ->selectRaw('item_id, SUM(quantity) as total, MAX(transaction_date) as tgl')
                ->groupBy('item_id')
                ->get()
                ->keyBy('item_id');

            return Item::with([
                    'category:id,name',
                    'unit:id,name',
                    'supplier:id,name',
                    'storageLocation:id,name'
                ])
                ->select([
                    'id', 'category_id', 'unit_id', 'supplier_id',
                    'storage_location_id', 'code', 'name', 'brand',
                    'stock', 'stock_minimum', 'stock_maximum',
                    'purchase_price', 'is_active'
                ])
                ->where('is_active', true)
                ->orderBy('name')
                ->get()
                ->map(function ($item) use ($transaksiMasuk, $transaksiKeluar) {
                    $masuk  = $transaksiMasuk->get($item->id);
                    $keluar = $transaksiKeluar->get($item->id);

                    $totalMasuk  = (int) ($masuk->total  ?? 0);
                    $totalKeluar = (int) ($keluar->total ?? 0);

                    $item->total_masuk  = $totalMasuk;
                    $item->total_keluar = $totalKeluar;
                    $item->stok_awal    = $item->stock - $totalMasuk + $totalKeluar;
                    $item->tgl_masuk    = $masuk->tgl  ?? null;
                    $item->tgl_keluar   = $keluar->tgl ?? null;

                    return $item;
                });
        });

        return response()->json([
            'status' => true,
            'data'   => $items,
        ]);
    }

    public function transactions(Request $request): JsonResponse
    {
        $type       = $request->type;
        $startDate  = $request->start_date;
        $endDate    = $request->end_date;

        $cacheKey = 'report_transactions_' . md5($type . $startDate . $endDate);

        $transactions = Cache::remember($cacheKey, 60, function () use ($type, $startDate, $endDate) {
            $query = Transaction::with([
                    'item:id,name,code,category_id,supplier_id',
                    'item.category:id,name',
                    'item.supplier:id,name',
                    'user:id,name'
                ])
                ->select([
                    'id', 'code', 'type', 'item_id', 'user_id',
                    'quantity', 'unit', 'price', 'note', 'transaction_date'
                ])
                ->orderBy('transaction_date', 'desc');

            if ($type) {
                $query->where('type', $type);
            }

            if ($startDate && $endDate) {
                $query->whereBetween('transaction_date', [$startDate, $endDate]);
            }

            return $query->limit(1000)->get();
        });

        return response()->json([
            'status' => true,
            'data'   => $transactions,
            'total'  => $transactions->count(),
        ]);
    }

    public function summary(): JsonResponse
    {
        $today     = now()->toDateString();
        $weekStart = now()->startOfWeek()->toDateString();
        $weekEnd   = now()->endOfWeek()->toDateString();

        $summary = Cache::remember('report_summary_' . $today, 60, function () use ($today, $weekStart, $weekEnd) {

            // Semua query summary dalam 1 kali ambil data
            $totalItems = Item::where('is_active', true)->count();

            $transaksiHariIni = Transaction::whereDate('transaction_date', $today)->count();

            $masukKeluar = Transaction::whereDate('transaction_date', $today)
                ->selectRaw("
                    SUM(CASE WHEN type = 'in'  THEN quantity ELSE 0 END) as masuk,
                    SUM(CASE WHEN type = 'out' THEN quantity ELSE 0 END) as keluar
                ")
                ->first();

            $masukKeluarMinggu = Transaction::whereBetween('transaction_date', [$weekStart, $weekEnd])
                ->selectRaw("
                    SUM(CASE WHEN type = 'in'  THEN quantity ELSE 0 END) as masuk,
                    SUM(CASE WHEN type = 'out' THEN quantity ELSE 0 END) as keluar
                ")
                ->first();

            $lowStock = Item::with([
                    'category:id,name',
                    'unit:id,name',
                    'supplier:id,name',
                    'storageLocation:id,name'
                ])
                ->select([
                    'id', 'category_id', 'unit_id', 'supplier_id',
                    'storage_location_id', 'code', 'name',
                    'stock', 'stock_minimum'
                ])
                ->where('is_active', true)
                ->whereColumn('stock', '<=', 'stock_minimum')
                ->orderBy('stock')
                ->get();

            return [
                'total_items'        => $totalItems,
                'transaksi_hari_ini' => $transaksiHariIni,
                'masuk_hari_ini'     => (int) ($masukKeluar->masuk  ?? 0),
                'keluar_hari_ini'    => (int) ($masukKeluar->keluar ?? 0),
                'masuk_minggu_ini'   => (int) ($masukKeluarMinggu->masuk  ?? 0),
                'keluar_minggu_ini'  => (int) ($masukKeluarMinggu->keluar ?? 0),
                'low_stock'          => $lowStock,
            ];
        });

        return response()->json([
            'status' => true,
            'data'   => $summary,
        ]);
    }

    public function export(Request $request)
{
    $format    = $request->format ?? 'pdf';
    $tab       = $request->tab ?? 'stok';
    $type      = $tab === 'masuk' ? 'in' : ($tab === 'keluar' ? 'out' : null);
    $startDate = $request->start_date;
    $endDate   = $request->end_date;
    $category  = $request->category;

    $filename  = 'laporan_' . $tab . '_' . now()->format('Ymd_His');

    $title = $tab === 'stok'
        ? 'Laporan Stok'
        : ($tab === 'masuk'
            ? 'Laporan Barang Masuk'
            : 'Laporan Barang Keluar');

    if ($tab === 'stok') {

        $transaksiMasuk = Transaction::where('type', 'in')
            ->selectRaw('item_id, SUM(quantity) as total, MAX(transaction_date) as tgl')
            ->groupBy('item_id')
            ->get()
            ->keyBy('item_id');

        $transaksiKeluar = Transaction::where('type', 'out')
            ->selectRaw('item_id, SUM(quantity) as total, MAX(transaction_date) as tgl')
            ->groupBy('item_id')
            ->get()
            ->keyBy('item_id');

        $query = Item::with([
                'category:id,name',
                'unit:id,name'
            ])
            ->where('is_active', true)
            ->orderBy('name');

        if ($category) {
            $query->whereHas('category', function ($q) use ($category) {
                $q->where('name', $category);
            });
        }

        $data = $query->get()->map(function ($item) use ($transaksiMasuk, $transaksiKeluar) {

            $masuk  = $transaksiMasuk->get($item->id);
            $keluar = $transaksiKeluar->get($item->id);

            $item->total_masuk  = (int) ($masuk->total ?? 0);
            $item->total_keluar = (int) ($keluar->total ?? 0);

            $item->stok_awal =
                $item->stock -
                $item->total_masuk +
                $item->total_keluar;

            $item->tgl_masuk  = $masuk->tgl ?? null;
            $item->tgl_keluar = $keluar->tgl ?? null;

            return $item;
        });

        if ($format === 'excel') {
            return Excel::download(
                new StockExport($data),
                $filename . '.xlsx'
            );
        }

    } else {

        $query = Transaction::with([
                'item:id,name,code,category_id,supplier_id',
                'item.category:id,name',
                'item.supplier:id,name',
                'user:id,name'
            ])
            ->where('type', $type)
            ->orderBy('transaction_date', 'desc');

        if ($startDate && $endDate) {
            $query->whereBetween(
                'transaction_date',
                [$startDate, $endDate]
            );
        }

        if ($category) {
            $query->whereHas('item.category', function ($q) use ($category) {
                $q->where('name', $category);
            });
        }

        $data = $query->limit(1000)->get();

        if ($format === 'excel') {
            return Excel::download(
                new TransactionExport($data, $tab),
                $filename . '.xlsx'
            );
        }
    }

        $pdf = Pdf::loadView('exports.report', compact('data', 'tab', 'title'))
        ->setPaper('a4', 'landscape');

        $pdfContent = $pdf->output();

        return response($pdfContent, 200, [
            'Content-Type'        => 'application/octet-stream',
            'Content-Disposition' => 'attachment; filename="' . $filename . '.pdf"',
            'Content-Length'      => strlen($pdfContent),
        ]);
    }
}
