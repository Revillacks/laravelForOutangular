<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
|
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router){
    Route::post('login', [userController::class, 'login']);
    Route::post('register', [userController::class, 'registerUser']);
    Route::post('logout', [userController::class, 'logout']);
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'rol'
], function ($router){
    Route::get('/roles', [RoleController::class, 'index']);
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'user'
], function ($router){
    Route::get('/users', [userController::class, 'getUsers']);
    Route::patch('/updateUser', [userController::class, 'editUser']);
    Route::delete('/deleteUser/{id}', [userController::class, 'deleteUser']);
    Route::get('/getUserById/{id}', [userController::class, 'getById']);

});
