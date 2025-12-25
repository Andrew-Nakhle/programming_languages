<?php

use App\Http\Controllers\ReservationController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FlatController;
Route::group(['middleware' => 'api',
    'prefix' => 'auth'], function () {
    Route::post('/register',[AuthController::class,'register']);
    Route::post('/login',[AuthController::class,'login']);
    Route::post('/logout',[AuthController::class,'logout'])->middleware('auth:api');
    Route::get('/me',[AuthController::class,'me'])->middleware('auth:api');
    Route::post('/refresh',[AuthController::class,'refresh'])->middleware('auth:api');
    Route::post('/update',[AuthController::class,'update'])->middleware('auth:api');
    Route::put('/update',[AuthController::class,'update'])->middleware('auth:api');
});
//Route::group(['middleware' => 'api',],
//Route::post('/createFlat', [FlatController::class, 'createFlat'])->middleware('auth:api');;
//Route::get('/showFlatById/{id}', [FlatController::class, 'showFlatsById'])->middleware('auth:api');;
//Route::post('/searchFlats', [FlatController::class, 'searchFlats'])->middleware('auth:api');
//Route::get('/showFlatsByUserId', [FlatController::class, 'showFlatsByUserId'])->middleware('auth:api');
//Route::delete('/deleteFlat/{id}', [FlatController::class, 'deleteFlat'])->middleware('auth:api');
//Route::put('/updateFlat/{id}', [FlatController::class, 'updateFlat'])->middleware('auth:api');
//)
Route::group(['middleware' => ['api']], function () {
    Route::post('/createFlat', [FlatController::class, 'createFlat'])->middleware('auth:api');
    Route::get('/showFlatById/{id}', [FlatController::class, 'showFlatsById'])->middleware('auth:api');
    Route::post('/searchFlats', [FlatController::class, 'searchFlats'])->middleware('auth:api');
    Route::get('/showFlatsByUserId', [FlatController::class, 'showFlatsByUserId'])->middleware('auth:api');
    Route::delete('/deleteFlat/{id}', [FlatController::class, 'deleteFlat'])->middleware('auth:api');
    Route::post('/updateFlat/{id}', [FlatController::class, 'updateFlat'])->middleware('auth:api');
    Route::put('/updateFlat/{id}', [FlatController::class, 'updateFlat'])->middleware('auth:api');;

});
Route::group(['middleware' => ['api']], function () {
    Route::post('/createReservation', [ReservationController::class, 'create'])->middleware('auth:api');
    Route::put('/updateReservation', [ReservationController::class, 'update'])->middleware('auth:api');
    Route::get('/cancelReservation/{id}', [ReservationController::class, 'cancelReservation'])->middleware('auth:api');
});

