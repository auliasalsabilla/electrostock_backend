<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\Item;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class NotificationController extends Controller
{
    public function index(): JsonResponse
    {
        $this->generateLowStockNotifications();

        $notifications = Notification::with('item:id,name,stock,stock_minimum')
            ->orderBy('is_read', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'status' => true,
            'data'   => $notifications,
            'total'  => $notifications->count(),
        ]);
    }

    public function markAsRead(Notification $notification): JsonResponse
    {
        $notification->update(['is_read' => true]);
        Cache::forget('notifications');

        return response()->json([
            'status'  => true,
            'message' => 'Notifikasi sudah ditandai sebagai dibaca.',
            'data'    => $notification,
        ]);
    }

    public function markAllAsRead(): JsonResponse
    {
        Notification::where('is_read', false)->update(['is_read' => true]);
        Cache::forget('notifications');

        return response()->json([
            'status'  => true,
            'message' => 'Semua notifikasi sudah ditandai sebagai dibaca.',
        ]);
    }

    private function generateLowStockNotifications(): void
    {
        // Hapus notifikasi lama yang sudah dibaca
        Notification::where('is_read', true)->delete();

        // Hapus notifikasi duplikat - hanya simpan 1 per item
        $duplicates = Notification::selectRaw('MIN(id) as keep_id, item_id')
            ->where('is_read', false)
            ->groupBy('item_id')
            ->get();

        if ($duplicates->count() > 0) {
            $keepIds = $duplicates->pluck('keep_id')->toArray();
            Notification::where('is_read', false)
                ->whereNotIn('id', $keepIds)
                ->delete();
        }

        // Ambil item dengan stok rendah
        $lowStockItems = Item::where('is_active', true)
            ->whereColumn('stock', '<=', 'stock_minimum')
            ->where('stock_minimum', '>', 0)
            ->get();

        foreach ($lowStockItems as $item) {
            $exists = Notification::where('item_id', $item->id)
                ->where('is_read', false)
                ->exists();

            if (!$exists) {
                $percentage = round(($item->stock / $item->stock_minimum) * 100);
                $status     = $percentage <= 30 ? 'critical' : 'warning';

                Notification::create([
                    'item_id' => $item->id,
                    'type'    => 'low_stock',
                    'message' => "Stok {$item->name} menipis! Saat ini {$item->stock} dari minimum {$item->stock_minimum} unit.",
                    'status'  => $status,
                    'is_read' => false,
                ]);
            }
        }
    }
}