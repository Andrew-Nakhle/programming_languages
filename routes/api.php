<?php
use  App\Http\Controllers\OtpController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\FlatRatingController;
use App\Http\Controllers\ReservationController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FlatController;
Route::group(['middleware' => 'api',
    'prefix' => 'auth'], function () {
    Route::post('/register',[AuthController::class,'register']);
    Route::post('/login',[AuthController::class,'login']);
    Route::post('/forgetPassword',[AuthController::class,'forgetPassword']);
    Route::patch('/resetPassword',[AuthController::class,'resetPassword']);
    Route::post('/deviceToken', [AuthController::class, 'storeDeviceToken'])->middleware('auth:api');
    Route::post('/logout',[AuthController::class,'logout'])->middleware('auth:api');
    Route::get('/me',[AuthController::class,'me'])->middleware('auth:api');
    Route::post('/refresh',[AuthController::class,'refresh'])->middleware('auth:api');
    Route::post('/update',[AuthController::class,'update'])->middleware('auth:api');
    Route::put('/update',[AuthController::class,'update'])->middleware('auth:api');

});

Route::group(['middleware' => ['api']], function () {
    Route::post('/createFlat', [FlatController::class, 'createFlat'])->middleware('auth:api');
    Route::get('/showFlatById/{id}', [FlatController::class, 'showFlatsById'])->middleware('auth:api');
    Route::post('/searchFlats', [FlatController::class, 'searchFlats'])->middleware('auth:api');
    Route::get('/showFlatsByUserId', [FlatController::class, 'showFlatsByUserId'])->middleware('auth:api');
    Route::delete('/deleteFlat/{id}', [FlatController::class, 'deleteFlat'])->middleware('auth:api');
    Route::post('/updateFlat/{id}', [FlatController::class, 'updateFlat'])->middleware('auth:api');
    Route::put('/updateFlat/{id}', [FlatController::class, 'updateFlat'])->middleware('auth:api');;
    Route::post('/flatRating/{id}', [FlatRatingController::class, 'createFlatRating'])->middleware('auth:api');
    Route::get('/showAvgRating/{id}', [FlatRatingController::class, 'avgRating'])->middleware('auth:api');

});
Route::group(['middleware' => ['api']], function () {
    Route::post('/createReservation', [ReservationController::class, 'create'])->middleware('auth:api');
    Route::put('/updateReservation', [ReservationController::class, 'update'])->middleware('auth:api');
    Route::patch('/cancelReservation/{id}', [ReservationController::class, 'cancelReservation'])->middleware('auth:api');
    Route::get('/showReservation',[ReservationController::class, 'showReservation'])->middleware('auth:api');
    Route::get('/ShowAllReservationsForUserFlats', [ReservationController::class, 'allReservationsForUserFlats'])->middleware('auth:api');

});
Route::group(['middleware'=>['api']],function(){
    Route::patch('approve/{id}',[AdminController::class, 'approveUser'])->middleware('auth:api','admin');
    Route::patch('reject/{id}',[AdminController::class, 'rejectUser'])->middleware('auth:api','admin');
    Route::get('showUsers',[AdminController::class, 'showUsers'])->middleware('auth:api','admin');
    Route::delete('delete/{id}',[AdminController::class, 'deleteUser'])->middleware('auth:api','admin');
});
////////////andrew was here/////////////////////////
Route::group(['middleware' => ['api']], function () {
    Route::patch('approveReservation/{id}', [ReservationController::class, 'approveReservation'])->middleware('auth:api');
    Route::patch('rejectReservation/{id}', [ReservationController::class, 'rejectReservation'])->middleware('auth:api');
});
/////////////andrew was here///////////////////////
Route::group(['middleware' => ['api']], function () {
    Route::post('addFavorite/{id}',[favoriteController::class, 'addToFavorite'])->middleware('auth:api');
    Route::delete('removeFavorite/{id}',[favoriteController::class, 'removeFromFavorite'])->middleware('auth:api');
    Route::get('showFavorite',[favoriteController::class, 'showFavorites'])->middleware('auth:api');
});
Route::group(['middleware' => ['api']], function () {
    Route::post('/otp', [OtpController::class, 'check']);
    Route::post('/verifyOtp', [OtpController::class, 'verifyOtp']);
});


