<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TransactionalMailApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Transactional Mail API endpoints authenticated via Laravel Sanctum.
|
*/

// Public Authentication Endpoint
Route::post('/authenticate', [TransactionalMailApiController::class, 'authenticate'])->name('api.authenticate');

// Protected Endpoints (auth:sanctum)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::get('/accounts', [TransactionalMailApiController::class, 'accounts'])->name('api.accounts');
    Route::post('/create_account', [TransactionalMailApiController::class, 'createAccount'])->name('api.create_account');
    Route::post('/queue_email', [TransactionalMailApiController::class, 'queueEmail'])->name('api.queue_email');
});
