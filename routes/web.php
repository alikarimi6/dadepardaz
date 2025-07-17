<?php

use App\Http\Controllers\api\v1\ExpenseAttachController;
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
