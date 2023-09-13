<?php

use App\Http\Controllers\admin\AdminAuthController;
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
        Route::post('login' , [AdminAuthController::class, 'login'])->name('admin.login');
    });


});