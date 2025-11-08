<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome');
})->name('home')->middleware('guest');

Route::middleware(['auth:admin'])->group(function () {
    Route::get('/dashboard', \App\Http\Controllers\Admin\DashboardController::class)->name('dashboard');
    Route::resource('category', \App\Http\Controllers\Admin\CategoryController::class);
    Route::resource('badge', \App\Http\Controllers\Admin\BadgeController::class);
    Route::resource('color', \App\Http\Controllers\Admin\ColorController::class);
    Route::resource('size', \App\Http\Controllers\Admin\SizeController::class);
    Route::resource('badge-charge', \App\Http\Controllers\Admin\BadgeMockupChargeController::class);
    Route::resource('mockup', \App\Http\Controllers\Admin\MockupController::class);
    Route::resource('mockup-side', \App\Http\Controllers\Admin\MockupSideController::class);
    Route::resource('variation-price', \App\Http\Controllers\Admin\MockupVariationController::class);
    Route::resource('vendor', \App\Http\Controllers\Admin\VendorController::class);
    Route::resource('store', \App\Http\Controllers\Admin\StoreController::class);
    Route::resource('product', \App\Http\Controllers\Admin\ProductController::class);
    Route::resource('campaign', \App\Http\Controllers\Admin\CampaignController::class);
    Route::resource('country', \App\Http\Controllers\Admin\CountryController::class);
    Route::get('/state/bulk-create',[\App\Http\Controllers\Admin\StateController::class, 'bulkCreate']);
    Route::post('/state-bulk-store',[\App\Http\Controllers\Admin\StateController::class, 'bulkStore'])->name('state.bulk.store');
    Route::resource('state', \App\Http\Controllers\Admin\StateController::class);
    Route::get('/city/bulk-create',[\App\Http\Controllers\Admin\CityController::class, 'bulkCreate']);
    Route::post('/city-bulk-store',[\App\Http\Controllers\Admin\CityController::class, 'bulkStore'])->name('city.bulk.store');
    Route::resource('city', \App\Http\Controllers\Admin\CityController::class);
    Route::resource('order', \App\Http\Controllers\Admin\OrderController::class);
    Route::resource('transaction', \App\Http\Controllers\Admin\TransactionController::class);
    Route::resource('report', \App\Http\Controllers\Admin\ReportController::class);
    Route::resource('order-status', \App\Http\Controllers\Admin\OrderStatusController::class);
    Route::resource('payment-method', \App\Http\Controllers\Admin\PaymentMethodController::class);
});



require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
