<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookingController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::prefix('bookings')->group(function () {
    Route::post('/booking-details', [BookingController::class, 'bookingDetails'])->name('store-bookingDetails');
    Route::post('/{booking_id}/children-details', [BookingController::class, 'childrenDetails'])->name('store-childrenDetails');
    Route::get('/{booking_id}/booking-summary', [BookingController::class, 'bookingSummary'])->name('get-bookingSummary');
});
