<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use \App\Http\Controllers\SaleController;
Route::get('/', function () {
    return view('welcome');
});
use App\Models\Product;
use Termwind\Components\Raw;

Route::resource('products', ProductController::class);
Route::resource('categories', \App\Http\Controllers\CategoryController::class);
Route::resource('movements', \App\Http\Controllers\StockMovementController::class);
Route::resource('sales', SaleController::class);
Route::get('/sales/{sale}/pdf', [SaleController::class, 'exportPdf'])->name('sales.pdf');
Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');