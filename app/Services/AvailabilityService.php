<?php

namespace App\Services;

use App\Models\Item;
use App\Models\Reservation;
use Carbon\Carbon;

class AvailabilityService
{
    /**
     * Ritorna la quantità disponibile per un item in un intervallo [start, end] (inclusivo).
     */
    public function availableQuantity(Item $item, string $startDate, string $endDate): int
    {
        $start = Carbon::parse($startDate)->startOfDay();
        $end   = Carbon::parse($endDate)->endOfDay();

        // Somma quantità di tutte le prenotazioni APPROVATE che si sovrappongono all'intervallo
        // Overlap condition: start_date <= end && end_date >= start
        $reserved = Reservation::where('item_id', $item->id)
            ->whereDate('start_date', '<=', $end)
            ->whereDate('end_date', '>=', $start)
            ->sum('quantity');

        return max(0, $item->quantity - $reserved);
    }

    /**
     * Lancia 422 se la richiesta eccede la disponibilità.
     */
    public function ensureAvailable(Item $item, string $startDate, string $endDate, int $requestedQty): void
    {
        $available = $this->availableQuantity($item, $startDate, $endDate);

        if ($requestedQty > $available) {
            abort(422, "Disponibilità insufficiente: disponibili {$available} nel periodo selezionato");
        }
    }
}