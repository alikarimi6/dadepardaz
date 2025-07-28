<?php

namespace App\Http\Middleware\Expense;

use App\Models\Expense;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckOwner
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $productId = $request->route('expense');

        $product = Expense::find($productId);
        if (!$product) {
            return response()->json( ['message' => 'محصول پیدا نشد.' ], 404);
        }

        if ($product->user_id != auth()->id()) {
            return response()->json(['message' => 'دسترسی ندارید.'] , 403);
        }
        return $next($request);
    }
}
