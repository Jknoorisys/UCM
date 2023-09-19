<?php

use App\Http\Controllers\admin\AdminAuthController;
use App\Http\Controllers\admin\AdminProfileController;
use App\Http\Controllers\admin\ManageUserController;
use App\Http\Controllers\user\UserAuthController;
use App\Http\Controllers\user\UserProfileController;
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
            Route::post('changepassword', [AdminProfileController::class, 'changePassword']);
            Route::post('getProfile', [AdminProfileController::class, 'getProfile']);
            Route::post('update-percent' , [AdminProfileController::class, 'updateBudgetPercentage']);
            Route::post('notifications', [AdminProfileController::class, 'getNotifications']);
            Route::post('send-notification', [AdminProfileController::class, 'sendNotification']);

            Route::prefix('users')->group(function () {
                Route::post('list' , [ManageUserController::class, 'getUserList']);
                Route::post('view' , [ManageUserController::class, 'getUserProfile']);
                Route::post('change-status' , [ManageUserController::class, 'userStatusChange']);
                Route::post('delete' , [ManageUserController::class, 'userDelete']);
            });

        });
    });

    Route::prefix('user')->group(function () {
        // By Javeriya Kauser
        Route::post('social-register' , [UserAuthController::class, 'socialRegistration']);
        Route::post('social-login' , [UserAuthController::class, 'socialLogin']);

        // By Aaisha Shaikh
        Route::post('register' , [UserAuthController::class, 'register']);
        Route::post('verifyOTP',[UserAuthController::class,'verifyOTP']);
        Route::post('resendregOTP',[UserAuthCOntroller::class,'resendRegOTP']);
        Route::post('login' , [UserAuthController::class, 'login']);
        Route::post('forgetpassword' , [UserAuthController::class, 'forgetpassword']);
        Route::post('forgotPasswordValidate',[UserAuthController::class,'forgotPasswordValidate']);

        Route::group(['middleware' => 'jwt.verify'], function () {
            Route::post('changepassword', [UserAuthController::class, 'changePassword']);
            Route::post('getProfile', [UserProfileController::class, 'getProfile']);

            // By Javeriya Kauser
            Route::post('delete-account', [UserProfileController::class, 'deleteAccount']);
            Route::post('notifications', [UserProfileController::class, 'getNotifications']);

            Route::prefix('snapchat')->group(function () {
                    // Matches The "/url/users" URL
            });
        });
    });
});