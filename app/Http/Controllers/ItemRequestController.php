<?php

namespace App\Http\Controllers;

use App\Models\ItemRequest;
use App\Models\Item;
use App\Services\AvailabilityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
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

    public function create(Request $r)
    {
        return Inertia::render('Requests/Create', [
            'items' => Item::where('status', 'available')
                ->with('category')
                ->orderBy('name')
                ->get(),
            'prefillItemId' => $r->integer('item_id') ?: null,
        ]);
    }

    public function store(Request $r, AvailabilityService $availability)
    {
        // Regole base
        $rules = [
            'type'       => 'required|in:inventory,to-buy',
            'item_id'    => 'nullable|exists:items,id',
            'quantity'   => 'required|integer|min:1',
            'start_date' => 'nullable|date',
            'end_date'   => 'nullable|date|after_or_equal:start_date',
            'note'       => 'nullable|string|max:1000',
        ];

        $v = Validator::make($r->all(), $rules);

        // Se è inventory → questi diventano required
        $v->sometimes('item_id', 'required', fn ($input) => $input->type === 'inventory');
        $v->sometimes('start_date', 'required', fn ($input) => $input->type === 'inventory');
        $v->sometimes('end_date', 'required', fn ($input) => $input->type === 'inventory');

        if ($v->fails()) {
            return back()
                ->withErrors($v)
                ->with('error', 'Per le richieste inventario compila tutti i campi richiesti.')
                ->withInput();
        }

        $data = $v->validated();

        if ($data['type'] === 'inventory') {
            // Pre-check disponibilità (feedback immediato)
            $item = Item::findOrFail($data['item_id']);
            try {
                $availability->ensureAvailable($item, $data['start_date'], $data['end_date'], (int) $data['quantity']);
            } catch (\Throwable $e) {
                return back()
                    ->with('error', $e->getMessage())
                    ->withInput();
            }
        } else {
            // Normalizza per to-buy
            $data['item_id']    = null;
            $data['start_date'] = null;
            $data['end_date']   = null;
        }

        $data['user_id'] = auth()->id();
        $data['status']  = 'pending';

        ItemRequest::create($data);

        return redirect()
            ->route('requests.mine')
            ->with('success', 'Richiesta inviata correttamente.');
    }
}