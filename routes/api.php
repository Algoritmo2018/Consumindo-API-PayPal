<?php

use App\Http\Controllers\TestEndpointsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('get/access/token',[TestEndpointsController::class, 'get_access_token']);
Route::post('create/order',[TestEndpointsController::class,'createOrder']);