<?php

namespace App\Http\Controllers;

use App\Models\ItemRequest;
use App\Models\Item;
use App\Services\AvailabilityService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ItemRequestController extends Controller
{
    public function index()
    {
        return Inertia::render('Requests/Mine', [
            'requests' => ItemRequest::where('user_id', auth()->id())
                ->with('item')
                ->latest()
                ->paginate(10),
        ]);
    }

    public function create()
    {
        return Inertia::render('Requests/Create', [
            'items' => Item::where('status', 'available')
                ->with('category')
                ->orderBy('name')
                ->get(),
            'prefillItemId' => request()->integer('item_id') ?: null,
        ]);
    }

    public function store(Request $r, AvailabilityService $availability)
    {
        $data = $r->validate([
            'type'       => 'required|in:inventory,to-buy',
            'item_id'    => 'nullable|exists:items,id',
            'quantity'   => 'required|integer|min:1',
            'start_date' => 'nullable|date',
            'end_date'   => 'nullable|date|after_or_equal:start_date',
            'note'       => 'nullable|string|max:1000',
        ]);

        if ($data['type'] === 'inventory') {
            // per richieste di inventario: item, start_date ed end_date sono obbligatori
            abort_unless(
                isset($data['item_id'], $data['start_date'], $data['end_date']),
                422,
                'Per le richieste inventario devi selezionare item e date'
            );

            // ✅ Pre-check disponibilità (anti-overlap): feedback immediato all’utente
            $item = Item::findOrFail($data['item_id']);
            $availability->ensureAvailable($item, $data['start_date'], $data['end_date'], (int) $data['quantity']);
        } else {
            // richieste "to-buy": niente item né date
            $data['item_id'] = null;
            $data['start_date'] = null;
            $data['end_date'] = null;
        }

        $data['user_id'] = auth()->id();
        $data['status']  = 'pending';

        ItemRequest::create($data);

        return redirect()->route('requests.mine')->with('success', 'Richiesta inviata');
    }
}