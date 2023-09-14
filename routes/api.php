<?php

use App\Http\Controllers\admin\AdminAuthController;
use App\Http\Controllers\user\UserAuthController;
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

Route::middleware(['localization'])->group(function () {

    // Admin Panel APIs
    Route::prefix('admin')->group(function () {
        Route::post('login' , [AdminAuthController::class, 'login']);

        Route::group(['middleware' => 'jwt.verify'], function () {
        Route::post('changepassword', [AdminAuthController::class, 'changePassword']);

        });
    });
    Route::prefix('user')->group(function () {
        Route::post('register' , [UserAuthController::class, 'register']);
        Route::post('verifyOTP',[UserAuthController::class,'verifyOTP']);
        Route::post('resendregOTP',[UserAuthCOntroller::class,'resendregOTP']);
        Route::post('login' , [UserAuthController::class, 'login']);
        Route::post('forgetpassword' , [UserAuthController::class, 'forgetpassword']);
        Route::post('forgotPasswordValidate',[UserAuthController::class,'forgotPasswordValidate']);
        Route::group(['middleware' => 'jwt.verify'], function () {
            Route::post('changepassword', [UserAuthController::class, 'changePassword']);
        });
    });


});