<?php

use App\Http\Controllers\admin\AdminAuthController;
use App\Http\Controllers\admin\AdminProfileController;
use App\Http\Controllers\admin\ManageContactUs;
use App\Http\Controllers\admin\ManageNotifications;
use App\Http\Controllers\admin\ManageUserController;
use App\Http\Controllers\user\ContactUsContoller;
use App\Http\Controllers\user\snapchat\AuthController as SnapchatAuthController;
use App\Http\Controllers\user\tiktok\AuthController as TiktokAuthController;
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

        // By Aaisha Shaikh
        Route::post('login' , [AdminAuthController::class, 'login']);

        Route::group(['middleware' => 'jwt.verify'], function () {
            // Admin profile By Aasiha Shaikh
            Route::post('changepassword', [AdminProfileController::class, 'changePassword']);
            Route::post('getProfile', [AdminProfileController::class, 'getProfile']);

            // By Javeriya Kauser
            Route::post('update-percent' , [AdminProfileController::class, 'updateBudgetPercentage']);

            // Manage Notifications By Javeriya Kauser
            Route::post('notifications', [ManageNotifications::class, 'getNotifications']);
            Route::post('send-notification', [ManageNotifications::class, 'sendNotification']);

            // Manage Contact Us By Javeriya Kauser
            Route::post('contact-us', [ManageContactUs::class, 'getContactUs']);

            // Manage Users By Aasiha Shaikh
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
        Route::post('contact-us', [ContactUsContoller::class, 'contactUs']);

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
            Route::post('linked-accounts', [UserProfileController::class, 'getLinkedAccounts']);

            Route::prefix('snapchat')->group(function () {
                Route::post('auth', [SnapchatAuthController::class, 'authorizeAccount']);
                Route::post('generate-token', [SnapchatAuthController::class, 'generateToken']);
            });

            Route::prefix('tiktok')->group(function () {
                Route::post('auth', [TiktokAuthController::class, 'authorizeAccount']);
                Route::post('generate-token', [TiktokAuthController::class, 'generateToken']);
            });
        });
    });
});