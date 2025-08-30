<?php

use Illuminate\Support\Facades\Route;

// Controllers
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\ItemController as AdminItemController;
use App\Http\Controllers\ItemRequestController;
use App\Http\Controllers\Admin\ItemRequestApprovalController as AdminReqController;

/*
|--------------------------------------------------------------------------
| Home: se autenticato → dashboard, altrimenti → login
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return auth()->check()
        ? to_route('dashboard')
        : to_route('login');
});

/*
|--------------------------------------------------------------------------
| Dashboard
|--------------------------------------------------------------------------
*/
Route::get('/dashboard', function () {
    return inertia('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

/*
|--------------------------------------------------------------------------
| Area profilo (utente autenticato)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| Admin area (solo role=admin)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'can:admin'])->group(function () {
    // Inventario
    Route::resource('/admin/items', AdminItemController::class)
        ->only(['index', 'store', 'update', 'destroy']);

    // Richieste utenti: approvazione/rifiuto
    Route::get('/admin/requests', [AdminReqController::class, 'index'])->name('admin.requests.index');
    Route::post('/admin/requests/{itemRequest}/approve', [AdminReqController::class, 'approve'])->name('admin.requests.approve');
    Route::post('/admin/requests/{itemRequest}/reject', [AdminReqController::class, 'reject'])->name('admin.requests.reject');
});

/*
|--------------------------------------------------------------------------
| User area: richieste proprie
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    Route::get('/requests', [ItemRequestController::class, 'index'])->name('requests.mine');
    Route::get('/requests/create', [ItemRequestController::class, 'create'])->name('requests.create');
    Route::post('/requests', [ItemRequestController::class, 'store'])->name('requests.store');
});

require __DIR__.'/auth.php';