<?php
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::apiResource('product', \App\Http\Controllers\API\ProductController::class)->only(['index', 'show'])->names('website.product');
Route::apiResource('collection', \App\Http\Controllers\API\CollectionController::class)->only(['index', 'show'])->names('website.collection');
Route::get('/cities', function(Request $request){
    $cites = City::query()->where('state_id', $request->state_id)->select('id', 'name','state_id')->get();
    return response()->json($cites);
});

//sslcommerz payment routes
Route::post('/success', [\App\Http\Controllers\API\SslCommerzPaymentController::class, 'success']);
Route::post('/fail', [\App\Http\Controllers\API\SslCommerzPaymentController::class, 'fail']);
Route::post('/cancel', [\App\Http\Controllers\API\SslCommerzPaymentController::class, 'cancel']);
Route::post('/ipn', [\App\Http\Controllers\API\SslCommerzPaymentController::class, 'ipn']);

// Customer Dashboard Routes
Route::get('/me', fn (Request $request) => $request->user())->middleware('auth:sanctum');
Route::prefix('/auth/customer')->group(function () {
    Route::post('/login', [App\Http\Controllers\Auth\SanctumAuthController::class, 'vendorLogin']);
    Route::post('/register', [App\Http\Controllers\Auth\SanctumAuthController::class, 'vendorRegister']);
    Route::post('/send-otp', [App\Http\Controllers\Auth\SanctumAuthController::class, 'sendOtp']);
    Route::post('/otp-verification', [App\Http\Controllers\Auth\SanctumAuthController::class, 'otpVerification']);
});
Route::prefix('customer')->middleware('auth:sanctum')->group(function () {
   
});