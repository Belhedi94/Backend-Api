<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\EmailVerificationController;
use App\Http\Controllers\ResetPasswordController;


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


//authentication/register routes
Route::post('/register', [RegisterController::class, 'register'])->middleware('guest');
Route::post('/login', [LoginController::class, 'authenticate'])->middleware('guest');


// link to be clicked when receiving the verification email
Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, '__invoke'])
    ->middleware(['signed', 'throttle:6,1'])
    ->name('verification.verify');
//forgot password routes
Route::post('/forgot-password', [ResetPasswordController::class, 'forgotPassword']);
Route::post('/reset-password', [ResetPasswordController::class, 'resetPassword']);

Route::middleware(['auth:sanctum', 'account.activated'])->group(function () {
    Route::post('/logout', [LoginController::class, 'logout']);

    Route::get('users/{user}', [UserController::class, 'show']);
    Route::post('/users/{user}', [UserController::class, 'update']);

    Route::middleware('admin')->group(function() {
        Route::post('admin/create', [AdminController::class, 'createUser']);
        Route::get('admin/list', [AdminController::class, 'getAdmins']);
        Route::patch('admin/ban/{id}', [AdminController::class, 'banUser']);
        Route::get('users', [UserController::class, 'index']);
        Route::delete('/users/{id}', [UserController::class, 'destroy']);
    });

    // Resend link to verify email
    Route::post('/email/verify/resend', function (Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('message', 'Verification link sent!');
    })->middleware('throttle:6,1')->name('verification.send');
    Route::get('/send/sms', [UserController::class, 'sendSmsNotification']);
});