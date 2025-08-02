<?php

use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\Expense\ExpenseCategoryController;
use App\Http\Controllers\Api\V1\Expense\ExpenseController;
use App\Http\Middleware\Expense\CheckOwner;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});
Route::prefix('v1')->group(function (){
    Route::prefix('auth')->name('auth.')->group(function (){
        Route::post('register', [AuthController::class, 'register'])->name('register');
        Route::post('login', [AuthController::class, 'login'])->name('login');
        Route::post('logout', [AuthController::class, 'logout'])->name('logout')
            ->middleware('auth:sanctum');
        Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
            return $request->user();
        });
    });
    Route::apiResource('expenses' , ExpenseController::class )->only(['show' , 'destroy'])->middleware(['auth:sanctum' , CheckOwner::class]);

    Route::middleware(['auth:sanctum' , 'role:supervisor|owner'])->prefix('owner')
        ->name('owner.')->group(function (){

        Route::get('expenses' ,[ExpenseController::class , 'getVerifiedBySupervisorExpenses'] )->name('expenses');
    });

//    supervisor routes
    Route::prefix('expenses')
        ->name('expenses.')
        ->middleware('auth:sanctum')
        ->group(function () {
            Route::apiResource('', ExpenseController::class)->only(['index', 'store']);
            Route::get('categories', [ExpenseCategoryController::class, 'index'])->name('categories');
            Route::middleware(['role:supervisor|owner'])->group(function () {
                Route::post('{expense}/approve', [ExpenseController::class, 'approve'])->name('approve');
                Route::post('{expense}/reject', [ExpenseController::class, 'reject'])->name('reject');
                Route::prefix('bulk')->name('bulk.')->group(function () {
                    Route::post('approves', [ExpenseController::class, 'bulkApprove'])->name('approve');
                    Route::post('rejects', [ExpenseController::class, 'bulkReject'])->name('reject');
                });
            });
        });
});
Route::get('token' , function (){
    $user = User::query()->find(1);
    return response()->json(['key' => $user->createToken('test' )->plainTextToken] , 200);
});

Route::get('test' , function (){
//    return response()->json(['data' => auth('sanctum')->user()->can('approve by owner')], 200);
});
Route::get('/rule', function () {
    $user = auth()->user();
    return response()->json(['rules' => $user->getRoleNames()]);
})->middleware('auth:sanctum');
