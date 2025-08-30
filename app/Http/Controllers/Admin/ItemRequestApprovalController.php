<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ItemRequest;
use App\Models\Item;
use App\Models\Reservation;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ItemRequestApprovalController extends Controller
{
    public function index()
    {
        $list = ItemRequest::with(['item','user'])->latest()->paginate(15);

        return Inertia::render('Admin/Requests/Index', [
            'requests' => $list,
        ]);
    }

    public function approve(ItemRequest $itemRequest)
    {
        abort_if($itemRequest->status !== 'pending', 400, 'Già deciso');

        if ($itemRequest->type === 'inventory') {
            $item = Item::findOrFail($itemRequest->item_id);

            if ($itemRequest->quantity > $item->quantity) {
                return back()->withErrors(['quantity' => 'Quantità non disponibile']);
            }

            Reservation::create([
                'item_request_id' => $itemRequest->id,
                'item_id'         => $item->id,
                'start_date'      => $itemRequest->start_date,
                'end_date'        => $itemRequest->end_date,
                'quantity'        => $itemRequest->quantity,
            ]);

            $item->decrement('quantity', $itemRequest->quantity);
        }

        $itemRequest->update(['status' => 'approved']);

        return back()->with('success', 'Richiesta approvata');
    }

    public function reject(ItemRequest $itemRequest, Request $r)
    {
        abort_if($itemRequest->status !== 'pending', 400, 'Già deciso');

        $itemRequest->update([
            'status' => 'rejected',
            'note'   => $r->input('note'),
        ]);

        return back()->with('success', 'Richiesta rifiutata');
    }
}