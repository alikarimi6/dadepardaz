<?php

use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\Expense\ExpenseCategoryController;
use App\Http\Controllers\Api\V1\Expense\ExpenseController;
use App\Http\Controllers\Api\V1\SuperAdmin\Role\PermissionController;
use App\Http\Controllers\Api\V1\SuperAdmin\Role\RoleController;
use App\Http\Controllers\Api\V1\SuperAdmin\StateController;
use App\Http\Controllers\Api\V1\SuperAdmin\TransitionController;
use App\Http\Controllers\Api\V1\SuperAdmin\TransitionRoleController;
use App\Http\Controllers\RoutePermissionController;
use App\Http\Middleware\Expense\CheckOwner;
use App\Http\Middleware\Route\CheckRoutePermission;
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
//        todo: define middleware coming from superadmin
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
//    superadmin routes:
Route::prefix('superadmin')->middleware(['auth:sanctum' , 'role:superadmin'])->name('superadmin.')->group(function () {
    Route::apiResource('roles', RoleController::class);
    Route::resource('permissions', PermissionController::class);
    Route::post('roles/{role}/permissions', [RoleController::class, 'assignPermissions'])->name('assign.permissions');
    Route::post('users/{user}/roles', [RoleController::class, 'assignRoles'])->name('assign.roles');
    Route::post('route/permissions', [RoutePermissionController::class, 'store'])->name('assign.route.permissions');

    Route::apiResource('states', StateController::class);
    Route::apiResource('transitions', TransitionController::class);
    Route::post('transitions/{transition}/roles', [TransitionRoleController::class, 'sync']);
    Route::post('transition/{transition}/list',  [TransitionRoleController::class, 'list']);
});
//

Route::prefix('panel')->middleware(['auth:sanctum', 'permission:view-expense-requests'])->name('panel.')->group(function () {
    Route::post('update/{expense}' , [ExpenseController::class, 'updateStatus'])->name('update.status');
});

});
Route::get('token' , function (){
    $user = User::query()->find(5);
    return response()->json(['key' => $user->createToken('test' )->plainTextToken ,
        'roles' => $user->roles,
        ] , 200);
});

Route::get('test' , function (){
//    return response()->json(['data' => auth('sanctum')->user()->can('approve by owner')], 200);
});
Route::get('/rule', function () {
    $user = auth()->user();
    return response()->json(['rules' => $user->getRoleNames()]);
})->middleware('auth:sanctum');
