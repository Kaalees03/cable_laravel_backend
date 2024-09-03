<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\admin\AuthController;  
use App\Http\Controllers\admin\TicketController;  
use App\Http\Controllers\user\UserAuthController;  


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

// Admin end //

// Auth
Route::post('admin_register',[AuthController::class,'admin_register']);
Route::post('admin_login',[AuthController::class,'admin_login']);
Route::post('forgot_password',[AuthController::class,'forgot_password']);
Route::post('otpcode_verification',[AuthController::class,'otpcode_verification']);
Route::post('resend_otpcode',[AuthController::class,'resend_otpcode']);
Route::post('change_password',[AuthController::class,'change_password']);
Route::post('update_password',[AuthController::class,'update_password']);

//Ticket
Route::post('create_ticket',[TicketController::class,'create_ticket']);

// User end //
Route::post('user_login',[UserAuthController::class,'user_login']);
Route::post('user_register',[UserAuthController::class,'user_register']);
Route::post('forgot_password',[UserAuthController::class,'forgot_password']);
Route::post('otpcode_verification',[UserAuthController::class,'otpcode_verification']);
Route::post('resend_otpcode',[UserAuthController::class,'resend_otpcode']);
Route::post('user_change_password',[UserAuthController::class,'user_change_password']);
Route::post('user_update_password',[UserAuthController::class,'user_update_password']);


