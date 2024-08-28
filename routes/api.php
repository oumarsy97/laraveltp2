<?php

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\UserController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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


Route::prefix('/v1/')->group(function () {
// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });
Route::apiResource('articles', ArticleController::class);

require base_path('./routes/userRoute.php');
require base_path('./routes/clientRoute.php');

Route::post('v1/auth/login', [AuthController::class, 'login']);
Route::post('v1/auth/refresh', [AuthController::class, 'refresh']);
// Route::middleware('auth:sanctum')->post('v1/auth/logout', [AuthController::class, 'logout']);
});





