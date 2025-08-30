<?php

namespace App\Http\Controllers;

use App\Models\ItemRequest;
use App\Models\Item;
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
            'items' => Item::where('status','available')->with('category')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $r)
    {
        $data = $r->validate([
            'type'         => 'required|in:inventory,to-buy',
            'item_id'      => 'nullable|exists:items,id',
            'quantity'     => 'required|integer|min:1',
            'start_date'   => 'nullable|date',
            'end_date'     => 'nullable|date|after_or_equal:start_date',
            'note'         => 'nullable|string|max:1000',
        ]);

        // regole minime: se inventory servono item e date
        if ($data['type'] === 'inventory') {
            abort_unless(isset($data['item_id'], $data['start_date'], $data['end_date']), 422, 'Dati incompleti');
        } else {
            // to-buy: non associare item/date
            $data['item_id'] = null;
            $data['start_date'] = null;
            $data['end_date'] = null;
        }

        $data['user_id'] = auth()->id();
        $data['status']  = 'pending';

        ItemRequest::create($data);

        return redirect()->route('requests.mine')->with('success','Richiesta inviata');
    }
}