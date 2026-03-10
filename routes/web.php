<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\SaleExportController;
use App\Http\Controllers\SaleImportController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\StoreExportController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::resource('sales', SaleController::class)->except(['show']);
    Route::get('sales-export', [SaleExportController::class, 'export'])->name('sales.export');
    Route::get('sales-import', [SaleImportController::class, 'show'])->name('sales.import.show');
    Route::post('sales-import', [SaleImportController::class, 'import'])->name('sales.import');
    Route::get('sales-import/template', [SaleImportController::class, 'template'])->name('sales.import.template');
    Route::resource('stores', StoreController::class)->except(['show']);
    Route::get('stores-export', [StoreExportController::class, 'export'])->name('stores.export');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';