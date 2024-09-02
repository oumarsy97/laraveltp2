<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\UserController;
use App\Models\User;

//users avec controller
Route::prefix('users')->group(function () {
    Route::get('/', [UserController::class, 'index'])->name('users.index');
    Route::get('/{id}', [UserController::class, 'show'])->name('users.show');
    Route::post('/', [UserController::class, 'store'])->name('users.store');
    Route::put('/{id}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/{id}', [UserController::class, 'destroy'])->name('users.destroy');
});

//users sans controller
// Route::get('api/v1/users', function () {
//     // Récupérer tous les utilisateurs
//     $users = User::all();
//     return response()->json([
//         'status' => 200,
//         'data' =>$users ,
//         'message' => 'Users retrieved successfully'
//     ]);
// });

// Route::get('api/v1/users/{id}', function ($id) {
//     // Récupérer un utilisateur par ID
//     $user = User::find($id);

//     if (!$user) {
//         return response()->json([
//             'status' => 404,
//             'data' => null,
//             'message' => 'User not found'
//         ], 404);
//     }

//     return response()->json([
//         'status' => 200,
//         'data' => $user,
//         'message' => 'User retrieved successfully'
//     ]);
// });
