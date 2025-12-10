<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FlatController;
Route::group(['middleware' => 'api',
    'prefix' => 'auth'], function ($router) {
    Route::post('/register',[AuthController::class,'register']);
    Route::post('/login',[AuthController::class,'login']);
    Route::post('/logout',[AuthController::class,'logout'])->middleware('auth:api');
    Route::get('/me',[AuthController::class,'me'])->middleware('auth:api');
    Route::post('/refresh',[AuthController::class,'refresh'])->middleware('auth:api');
});

Route::post('/createFlat',[FlatController::class,'createFlat'])->middleware('auth:api');;
Route::get('/showFlatById/{id}',[FlatController::class,'showFlatsById'])->middleware('auth:api');;
Route::get('/searchFlats',[FlatController::class,'searchFlats'])->middleware('auth:api');
Route::get('/showFlatsByUserId',[FlatController::class,'showFlatsByUserId'])->middleware('auth:api');
Route::delete('/deleteFlat/{id}',[FlatController::class,'deleteFlat'])->middleware('auth:api');
Route::put('/updateFlat/{id}',[FlatController::class,'updateFlat'])->middleware('auth:api');
