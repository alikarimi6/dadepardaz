<?php

namespace App\Http\Controllers\Api\V1\Expense;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class ExpenseAttachController extends Controller
{
    public function download($filepath)
    {
        if (!Storage::disk(config('filesystem.custom_upload_disk'))->exists($filepath)) {
            abort(404);
        }

        $file = Storage::disk(config('filesystem.custom_upload_disk'))->path($filepath);

        return response()->download($file);
    }
}
