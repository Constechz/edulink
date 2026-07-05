<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\v1\AuthApiController;
use App\Http\Controllers\Api\v1\StudentApiController;
use App\Http\Controllers\Api\v1\ScoringApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider or bootstrap/app.php.
|
*/

Route::prefix('v1')->group(function () {
    // Public / Authentication endpoints
    Route::post('/auth/login', [AuthApiController::class, 'login']);

    // Authenticated API endpoints (requires api.key middleware)
    Route::middleware('api.key')->group(function () {
        // Students API
        Route::get('/students', [StudentApiController::class, 'index']);
        Route::post('/students', [StudentApiController::class, 'store']);
        Route::get('/students/{id}', [StudentApiController::class, 'show']);

        // Scoring Configurations & Records API
        Route::get('/scoring/configurations', [ScoringApiController::class, 'getConfigurations']);
        Route::post('/scoring/configurations', [ScoringApiController::class, 'storeConfiguration']);
        
        Route::get('/scoring/scores', [ScoringApiController::class, 'getScores']);
        Route::post('/scoring/scores', [ScoringApiController::class, 'storeScore']);
        Route::post('/scoring/scores/bulk', [ScoringApiController::class, 'bulkStoreScores']);
    });
});
