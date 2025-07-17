<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ExpenseAttachController extends Controller
{
    public function download($filepath)
    {
        $file = storage_path("app/public/$filepath");

        if (!file_exists($file)) {
            abort(404);
        }

        return response()->download($file);
    }
}
