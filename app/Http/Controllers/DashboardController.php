<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\ItemRequest;
use App\Models\Reservation;
use App\Models\User;
use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(Request $r)
    {
        $now    = Carbon::now();
        $from30 = $now->copy()->subDays(30)->startOfDay();

        $isAdmin   = auth()->user()?->role === 'admin';
        $myPending = ItemRequest::where('user_id', auth()->id())->where('status', 'pending')->count();
        $myApproved= ItemRequest::where('user_id', auth()->id())->where('status', 'approved')->count();

        $adminKpi = null;
        if ($isAdmin) {
            $adminKpi = [
                'totalItems'        => Item::count(),
                'pendingRequests'   => ItemRequest::where('status','pending')->count(),
                'approvedThisMonth' => ItemRequest::where('status','approved')
                    ->whereBetween('updated_at', [$now->copy()->startOfMonth(), $now->copy()->endOfMonth()])
                    ->count(),
                'activeToday'       => Reservation::whereDate('start_date','<=',$now)
                    ->whereDate('end_date','>=',$now)->sum('quantity'),
                'topItem' => (function () use ($from30, $now) {
                    $row = Reservation::select('item_id', DB::raw('SUM(quantity) as q'))
                        ->whereDate('created_at', '>=', $from30)
                        ->groupBy('item_id')
                        ->orderByDesc('q')->first();
                    if (!$row) return null;
                    $item = Item::select('id','name')->find($row->item_id);
                    return [
                        'id'    => $item?->id,
                        'name'  => $item?->name,
                        'qty'   => (int)$row->q,
                        'range' => $from30->toDateString().' → '.$now->toDateString(),
                    ];
                })(),
                'topUser' => (function () use ($from30, $now) {
                    $row = ItemRequest::select('user_id', DB::raw('COUNT(*) as c'))
                        ->whereDate('created_at','>=',$from30)
                        ->groupBy('user_id')
                        ->orderByDesc('c')->first();
                    if (!$row) return null;
                    $u = User::select('id','name','email')->find($row->user_id);
                    return [
                        'id'    => $u?->id,
                        'name'  => $u?->name,
                        'email' => $u?->email,
                        'count' => (int)$row->c,
                        'range' => $from30->toDateString().' → '.$now->toDateString(),
                    ];
                })(),
            ];
        }

        // Catalogo (solo utenti non admin)
        $catalog = null; $categories = null; $filters = null;
        if (!$isAdmin) {
            $filters = [
                'q'           => trim((string)$r->query('q', '')),
                'category_id' => $r->query('category_id'),
                'status'      => $r->query('status', 'available'),
            ];

            $q = Item::query()->with('category');
            if ($filters['status'])      $q->where('status', $filters['status']);
            if ($filters['category_id']) $q->where('category_id', $filters['category_id']);
            if ($filters['q'] !== '')    $q->where('name', 'like', '%'.$filters['q'].'%');

            $catalog    = $q->orderBy('name')->paginate(10)->withQueryString();
            $categories = Category::orderBy('name')->get(['id','name']);
        }

        return Inertia::render('Dashboard', [
            'isAdmin'    => $isAdmin,
            'myKpi'      => ['pending'=>$myPending, 'approved'=>$myApproved],
            'adminKpi'   => $adminKpi,
            'catalog'    => $catalog,
            'categories' => $categories,
            'filters'    => $filters,
        ]);
    }
}