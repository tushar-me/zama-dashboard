<?php
use App\Models\City;
use App\Models\Country;
use App\Models\State;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


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

// Customer Dashboard Routes
Route::get('/me', function (Request $request) {return $request->user();})->middleware('auth:sanctum');
Route::prefix('/auth/customer')->group(function () {
    Route::post('/login', [App\Http\Controllers\Auth\SanctumAuthController::class, 'vendorLogin']);
    Route::post('/register', [App\Http\Controllers\Auth\SanctumAuthController::class, 'vendorRegister']);
    Route::post('/send-otp', [App\Http\Controllers\Auth\SanctumAuthController::class, 'sendOtp']);
    Route::post('/otp-verification', [App\Http\Controllers\Auth\SanctumAuthController::class, 'otpVerification']);
});
Route::prefix('customer')->middleware('auth:sanctum')->group(function () {
   
});