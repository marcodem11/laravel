<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Item;
use App\Models\Category;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ItemController extends Controller
{
    // elimina il __construct con $this->middleware(...)

    public function index()
    {
        return Inertia::render('Admin/Items/Index', [
            'items' => Item::with('category')->latest()->paginate(10),
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    public function store(Request $r)
    {
        $data = $r->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'quantity' => 'required|integer|min:1',
            'status' => 'required|in:available,unavailable',
        ]);
        Item::create($data);
        return back()->with('success', 'Item creato');
    }

    public function update(Request $r, Item $item)
    {
        $data = $r->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'quantity' => 'required|integer|min:1',
            'status' => 'required|in:available,unavailable',
        ]);
        $item->update($data);
        return back()->with('success', 'Item aggiornato');
    }

    public function destroy(Item $item)
    {
        $item->delete();
        return back()->with('success', 'Item eliminato');
    }
}