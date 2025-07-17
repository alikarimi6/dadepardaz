<?php
/*
namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Services\Bank\BankResolver;
use App\Services\Bank\PaymentService;
use App\Services\Bank\SamanBank;
use App\Services\Bank\SarmayeBank;
use App\Services\Bank\TejaratBank;
use Exception;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    private PaymentService $paymentService;
    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function pay(Request $request)
    {
        $request->validate([
            'iban' => 'required|string|size:26',
            'amount' => 'required|numeric|min:100',
        ]);

        $iban = $request->input('iban');
        $amount = $request->input('amount');

        try {
            $this->paymentService->pay($request->iban, $request->amount);
            return response()->json(['status' => 'success']);

        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}*/
