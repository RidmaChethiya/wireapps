<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\MedicationController;
use App\Http\Controllers\CustomerController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('/user/registration', [UserController::class, 'register']);
Route::post('/user/login', [UserController::class, 'login']);

//token protection
Route::group(['middleware' => ['auth:sanctum']], function () {
    
    // Route::put('/user/update/{id}', [UserController::class, 'update']);
    Route::put('/user/update_role/{id}', [UserController::class, 'updateRole']);

    Route::get('/role', [RoleController::class, 'index']);
    Route::post('/role/save', [RoleController::class, 'store']);
    Route::put('/role/update/{id}', [RoleController::class, 'update']);
    Route::delete('/role/delete/{id}', [RoleController::class, 'destroy']);
    

    Route::get('/permission', [PermissionController::class, 'index']);
    Route::post('/permission/save', [PermissionController::class, 'store']);
    Route::put('/permission/update/{id}', [PermissionController::class, 'update']);
    Route::delete('/permission/delete/{id}', [PermissionController::class, 'destroy']);
    
    Route::get('/medication', [MedicationController::class, 'index']);
    Route::post('/medication/save', [MedicationController::class, 'store']);
    Route::put('/medication/update/{id}', [MedicationController::class, 'update']);
    Route::delete('/medication/delete/{id}', [MedicationController::class, 'destroy']);
    
    Route::get('/customer', [CustomerController::class, 'index']);
    Route::post('/customer/save', [CustomerController::class, 'store']);
    Route::put('/customer/update/{id}', [CustomerController::class, 'update']);
    Route::delete('/customer/delete/{id}', [CustomerController::class, 'destroy']);

    Route::post('/user/logout', [UserController::class, 'logout']);

});

