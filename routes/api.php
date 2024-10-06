<?php
/* Borozepped */

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::get('/user', [AuthController::class, 'getUser']);
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::apiResource('products', ProductController::class);
});
Route::post('/login', [AuthController::class, 'login']);

