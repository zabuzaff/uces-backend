<?php

use App\Http\Controllers\AuthApiController;
use App\Http\Controllers\NotificationApiController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['middleware' => 'guest'], function () {
    Route::post('/register', [AuthApiController::class, 'register'])->name('auth.register');
    Route::post('/login', [AuthApiController::class, 'login'])->name('auth.login');
});

Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::post('/logout', [AuthApiController::class, 'logout'])->name('auth.logout');
    Route::post('/edit-profile', [AuthApiController::class, 'editProfile'])->name('auth.edit-profile');
    Route::post('/upload-avatar', [AuthApiController::class, 'uploadAvatar'])->name('auth.upload-avatar');
    Route::post('/change-password', [AuthApiController::class, 'changePassword'])->name('auth.change-password');
    Route::get('/notifications', [NotificationApiController::class, 'getNotifications'])->name('notification.get');
    Route::put('/notifications/mark-as-read', [NotificationApiController::class, 'markAsRead'])->name('notification.mark-as-read');

    include __DIR__ . '/generated-api-resources.php';
});
