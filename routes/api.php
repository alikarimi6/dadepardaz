<?php

use App\Http\Controllers\api\v1\ExpenseCategoryController;
use App\Http\Controllers\Api\v1\ExpenseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (){
    Route::prefix('expenses')->name('expenses.')->group(function (){
    Route::apiResource('' , ExpenseController::class );
    Route::get('categories', [ExpenseCategoryController::class, 'index'])->name('categories');
    Route::post('{expense}/approve', [ExpenseController::class, 'approve'])->name('approve');
    Route::post('{expense}/reject', [ExpenseController::class, 'reject'])->name('reject');
    Route::prefix('bulk')->name('bulk.')->group(function (){
        Route::post('approves' , [ExpenseController::class , 'bulkApprove'])->name('approve') ;
        Route::post('rejects' , [ExpenseController::class , 'bulkReject'])->name('reject') ;
    });
    });

});
