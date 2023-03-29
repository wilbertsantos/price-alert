<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AlertController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => 'alerts'], function () {
    Route::post('/', [AlertController::class, 'store'])->name('alerts.create');
    Route::get('/', [AlertController::class, 'index'])->name('alerts.index');
    Route::get('/{id}', [AlertController::class, 'show'])->name('alerts.show');
    Route::put('/{id}', [AlertController::class, 'update'])->name('alerts.edit');
    Route::delete('/{id}', [AlertController::class, 'destroy'])->name('alerts.destroy');
});
