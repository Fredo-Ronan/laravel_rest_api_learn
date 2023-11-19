<?php

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('/register', [App\Http\Controllers\Api\AuthController::class, 'register']);
Route::post('/login', [App\Http\Controllers\Api\AuthController::class,'login']);


Route::group(['middleware' => 'auth:api'], function (){
    //Content
    Route::get('/contents', [App\Http\Controllers\Api\ContentController::class, 'index']);
    Route::post('/contents', [App\Http\Controllers\Api\ContentController::class, 'store']);
    Route::get('/contents/{id}', [App\Http\Controllers\Api\ContentController::class, 'show']);
    Route::put('/contents/{id}', [App\Http\Controllers\Api\ContentController::class, 'update']);
    Route::delete('/contents/{id}', [App\Http\Controllers\Api\ContentController::class, 'destroy']);

    //Activities
    Route::get('/activities', [App\Http\Controllers\Api\ActivitiesController::class, 'index']);
    Route::post('/activities', [App\Http\Controllers\Api\ActivitiesController::class, 'store']);
    Route::get('/activities/{id}', [App\Http\Controllers\Api\ActivitiesController::class, 'show']);
    Route::put('/activities/{id}', [App\Http\Controllers\Api\ActivitiesController::class, 'update']);
    Route::delete('/activities/{id}', [App\Http\Controllers\Api\ActivitiesController::class, 'destroy']);
    //Show Activities beda metode
    Route::get('/activities/{id}', [App\Http\Controllers\Api\ActivitiesController::class, 'customShowData']);

    //User RUDS (Read, Update, Delete, Show)
    Route::get('/users', [App\Http\Controllers\Api\UserController::class, 'index']);
    Route::get('/users/{id}', [App\Http\Controllers\Api\UserController::class, 'show']);

    // Untuk method PUT ini ternyata postman di tempat saya 
    // x-www-form-urlencoded gabisa diganti input typenya jadi File yang bisa upload image.
    // Jadi sementara caranya tetap pake method POST tapi key inputnya ditambahi 1 field lagi yang isinya seperti dibawah ini
    // key: _method
    // value: PUT
    // supaya method sebenarnya jadi PUT, padahal dari postman method yang dipilih POST
    Route::put('/users/{id}', [App\Http\Controllers\Api\UserController::class, 'update']);
    Route::delete('/users/{id}', [App\Http\Controllers\Api\UserController::class, 'destroy']);

    //Subscriptions
    Route::get('/subscriptions', [App\Http\Controllers\Api\SubscriptionController::class, 'index']);
    Route::post('/subscriptions', [App\Http\Controllers\Api\SubscriptionController::class, 'store']);
    Route::get('/subscriptions/{id}', [App\Http\Controllers\Api\SubscriptionController::class, 'show']);
    Route::put('/subscriptions/{id}', [App\Http\Controllers\Api\SubscriptionController::class, 'update']);
    Route::delete('/subscriptions/{id}', [App\Http\Controllers\Api\SubscriptionController::class, 'destroy']);
});