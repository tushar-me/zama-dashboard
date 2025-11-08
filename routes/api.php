<?php
use App\Models\City;
use App\Models\Country;
use App\Models\State;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/product-images/{slug}',[App\Http\Controllers\Api\Website\V1\ProductController::class, 'images']);
Route::get('/product-variation/{slug}',[App\Http\Controllers\Api\Website\V1\ProductController::class, 'variationPrice']);
Route::apiResource('product', App\Http\Controllers\Api\Website\V1\ProductController::class)->only(['index','show'])->names('website.product');
Route::apiResource('campaign', App\Http\Controllers\Api\Website\V1\CampaignController::class)->only(['index','show'])->names('website.campaign');
Route::get('/order-summary', [App\Http\Controllers\Api\Website\V1\CartController::class, 'orderSummary']);
Route::apiResource('cart', App\Http\Controllers\Api\Website\V1\CartController::class)->names('website.cart');
Route::apiResource('orders', App\Http\Controllers\Api\Website\V1\OrderController::class)->names('website.order');
Route::get('/paypal-payment-success', [App\Http\Controllers\Api\Website\V1\PayalController::class, 'paymentSuccess']);
Route::get('/paypal-payment-cancel', [App\Http\Controllers\Api\Website\V1\PayalController::class, 'paymentCancel']);

Route::get('/countries', function(){
    return Country::query()->select(['id','name','code','phone_code'])->get();
});
Route::get('/states', function(Request $request){
    $states = State::query()->where('country_id', $request->country_id)->select('id', 'name','country_id')->get();
    return response()->json($states);
});
Route::get('/cities', function(Request $request){
    $cites = City::query()->where('state_id', $request->state_id)->select('id', 'name','state_id')->get();
    return response()->json($cites);
});

// Vendor Dashboard Routes
Route::get('/me', function (Request $request) {return $request->user();})->middleware('auth:sanctum');
Route::prefix('v1/auth/vendor')->group(function () {
    Route::post('/login', [App\Http\Controllers\Auth\SanctumAuthController::class, 'vendorLogin']);
    Route::post('/register', [App\Http\Controllers\Auth\SanctumAuthController::class, 'vendorRegister']);
    Route::post('/send-otp', [App\Http\Controllers\Auth\SanctumAuthController::class, 'sendOtp']);
    Route::post('/otp-verification', [App\Http\Controllers\Auth\SanctumAuthController::class, 'otpVerification']);
    Route::post('/store/login/{id}', [App\Http\Controllers\Api\Store\V1\StoreController::class, 'login'])->middleware('auth:sanctum');
});
Route::prefix('v1/store')->middleware('auth:sanctum')->group(function () {
    Route::get('/category', [App\Http\Controllers\Api\Store\V1\ProductController::class, 'categories']);
    Route::get('/mockup', [App\Http\Controllers\Api\Store\V1\ProductController::class, 'mockups']);
    Route::post('/create-campaign-product', [App\Http\Controllers\Api\Store\V1\CampaignController::class, 'createProduct']);
    Route::post('/attach-artwork-to-campaign/{id}', [App\Http\Controllers\Api\Store\V1\CampaignController::class, 'attachArtworkToCampaign']);
    Route::post('/attach-artwork-to-product-side/{id}', [App\Http\Controllers\Api\Store\V1\ProductController::class, 'attachArtworkToProductSide']);
    Route::apiResource('store', App\Http\Controllers\Api\Store\V1\StoreController::class)->names('seller.store');
    Route::apiResource('campaign', App\Http\Controllers\Api\Store\V1\CampaignController::class)->names('seller.campaign');
    Route::post('/clone-campaign/{id}', [App\Http\Controllers\Api\Store\V1\CampaignController::class, 'clone']);
    Route::apiResource('product', App\Http\Controllers\Api\Store\V1\ProductController::class)->names('seller.product');
    Route::post('/product/reorder', [App\Http\Controllers\Api\Store\V1\ProductController::class, 'reorder']);
    Route::post('/update-product-price/{id}', [App\Http\Controllers\Api\Store\V1\ProductController::class, 'updateProductPrice']);
    Route::delete('/remove-artwork/{id}',  [App\Http\Controllers\Api\Store\V1\ProductController::class, 'removeArtwork']);
    Route::post('/product-alloverprint-images', [App\Http\Controllers\Api\Store\V1\ProductController::class, 'storeAllOverPrintImages']);
    Route::post('/product-color/{id}', [App\Http\Controllers\Api\Store\V1\ProductController::class, 'manageColor']);
    Route::apiResource('store-front', App\Http\Controllers\Api\Store\V1\StoreFrontController::class);
    Route::get('/payment-methods', [App\Http\Controllers\Api\Store\V1\PaymentAccountController::class, 'paymentMethods']);
    Route::apiResource('payment-account', App\Http\Controllers\Api\Store\V1\PaymentAccountController::class);
    Route::apiResource('seller-order', \App\Http\Controllers\Api\Store\V1\OrderController::class);
    Route::apiResource('payout-request', \App\Http\Controllers\Api\Store\V1\PayoutRequestController::class);
});