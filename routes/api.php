<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DetteController;
use App\Http\Controllers\UserController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use L5Swagger\L5Swagger;

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

Route::get('/api/documentation', function () {
    return view('vendor.l5-swagger');
});
Route::post('/v1/login', [AuthController::class, 'login']);
Route::post('/v1/logout', [AuthController::class, 'logout']);

Route::prefix('/v1/')->group(function () {

Route::middleware(['auth:api'])->group(function () {
    Route::middleware(['auth:api', 'role:admin'])->group(function () {

    });
    Route::middleware('role:admin,boutiquier')->group(function () {
    Route::apiResource('users', UserController::class);
    Route::post('register', [AuthController::class, 'store']);
    Route::apiResource('clients', ClientController::class);
    Route::apiResource('dettes', DetteController::class)->except(['listArticleDette','listPaiementDette']);
    Route::post('clients/telephone', [ClientController::class, 'findByTelephone']);
    Route::get('clients/{id}/user', [ClientController::class, 'findUser']);

});
    Route::post('dettes/{id}/articles',[DetteController::class, 'listArticleDette'] );
    Route::get('dettes/{id}/paiements',[DetteController::class, 'listPaiementDette'] );
    Route::post('dettes/{id}/paiements',[DetteController::class, 'paiementDette'] );
    Route::get('clients/{id}/dettes', [ClientController::class, 'findDettes']);
    Route::post('articles/stock', [ArticleController::class, 'updateStock']);
    Route::post('articles/libelle', [ArticleController::class, 'findbyLibelle']);
    Route::apiResource('articles', ArticleController::class)->except(['findbyLibelle', 'updateStock']);

});

Route::post('auth/register', [AuthController::class, 'register']);
Route::post('auth/refresh', [AuthController::class, 'refresh']);
// Route::middleware('auth:sanctum')->post('v1/auth/logout', [AuthController::class, 'logout']);
});





