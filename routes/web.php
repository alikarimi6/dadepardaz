<?php

use App\Http\Controllers\Api\V1\Expense\ExpenseAttachController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('index');
});

Route::get('expense' , function (){
    return view('expense.index');
});
Route::get('expenses/create' , function (){
    return view('expense.create');
})->name('expenses.create.view');

Route::get('admin/expenses' , function (){
   return view('admin.expenses');
})->name('expenses.view');

Route::get('/download/{filepath}', [ExpenseAttachController::class, 'download'])
    ->name('expense.attachment.download')
    ->where('filepath', '.*');


//Route::get('/register', [RegisterController::class, 'showRegisterForm'])
////    ->middleware('guest')
//    ->name('register');
//Route::post('/register', [RegisterController::class, 'register'])
////    ->middleware('guest')
//    ->name('submit-register');
//Route::get('/login', [LoginController::class, 'showLoginForm'])
////    ->middleware('guest')
//    ->name('login');
/*Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])
    ->middleware('guest')
    ->name('password.request');
Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])
    ->middleware('guest')
    ->name('password.reset');*/

