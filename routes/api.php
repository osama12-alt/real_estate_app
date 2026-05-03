<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PropertyController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| هنا يتم تعريف جميع مسارات الـ API الخاصة بالمشروع.
| كل المسارات المحمية تحتاج auth:sanctum.
|
*/

// مسارات عامة (بدون تسجيل دخول)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login',    [AuthController::class, 'login']);

// مسارات محمية (تحتاج توكن)
Route::middleware('auth:sanctum')->group(function () {

    // معلومات المستخدم الحالي
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // تسجيل الخروج
    Route::post('/logout', [AuthController::class, 'logout']);

    /*
    |--------------------------------------------------------------------------
    | Property Routes
    |--------------------------------------------------------------------------
    */

    // عرض العقارات حسب الدور
    Route::get('/properties', [PropertyController::class, 'index']);

    // إضافة عقار (المالك + المدير)
    Route::post('/properties', [PropertyController::class, 'store']);

    // تعديل العقار
    Route::put('/properties/{property}', [PropertyController::class, 'update']);

    // حذف العقار
    Route::delete('/properties/{property}', [PropertyController::class, 'destroy']);
});
