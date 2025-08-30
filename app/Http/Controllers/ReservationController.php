<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use Inertia\Inertia;

class ReservationController extends Controller
{
    public function index()
    {
        $reservations = Reservation::whereHas('request', fn($q) =>
                $q->where('user_id', auth()->id())->where('status','approved')
            )
            ->with(['item', 'request'])
            ->latest()
            ->paginate(10);

        return Inertia::render('Reservations/Index', [
            'reservations' => $reservations,
        ]);
    }
}