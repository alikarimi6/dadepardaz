<?php

namespace App\Http\Controllers\Api\V1\Expense;

use App\Events\ExpenseApproved;
use App\Events\ExpenseRejected;
use App\Http\Controllers\Controller;
use App\Http\Requests\Expense\ApproveRequest;
use App\Http\Requests\Expense\RejectRequest;
use App\Http\Requests\Expense\StoreExpenseRequest;
use App\Http\Resources\Api\V1\ExpenseResource;
use App\Models\Expense;
use App\Models\User;
use App\Services\Expense\ExpenseStateService;
use App\States\Payment\Requested;
use App\States\Payment\VerifiedByOwner;
use App\States\Payment\VerifiedBySupervisor;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Storage;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        $expenses = Expense::with(['user', 'category', 'attachment'])->orderByDesc('id')->get();
        return response()->json(ExpenseResource::collection($expenses), 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreExpenseRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['user_id'] = User::query()->whereHas('ibans', function ($query) use ($data) {
            $query->where('code', $data['iban']);
        })->value('id');
        $expense = Expense::create($data);

/**
 * upload file
*/
        if ($request->hasFile('attachment')) {
            $this->storeAttachFile($expense , $request->file('attachment'));
        }

        return response()->json(['message' => 'expense created.' , 'expense' => ExpenseResource::make($expense) ] , 201);
    }

    private function storeAttachFile(Expense $expense , $file): void
    {
        $dir = Config::get('storage_paths.expense_attachments');
        $firstFile = is_array($file) ? $file[0] : $file;
        $path = Storage::disk('public')->put($dir , $firstFile);
        $expense->attachment()->create([
            'file_path' => $path
        ]);
    }
    public function approve(ApproveRequest $request ,Expense $expense): JsonResponse
    {
//        check role
//        if owner and state is not approved by admin don't show
//        if supervisor and state is requested show
/*        if(auth()->user()->hasRole('owner') && $expense->state == VerifiedByOwner::$name){
            event(new ExpenseApproved(
                    $expense->user()->first() , $expense , $request->payment_method )
            );
            return response()->json([
                'message' => 'expense approved',
                'expense' => ExpenseResource::make($expense),]);
        }
        if (auth()->user()->hasRole('supervisor') && $expense->state == Requested::$name){
            event(new ExpenseApproved(
                    $expense->user()->first() , $expense , $request->payment_method )
            );
            return response()->json([
                'message' => 'expense approved',
                'expense' => ExpenseResource::make($expense),]);
        }*/
        $expenseStateService = new ExpenseStateService();
        return $expenseStateService->tryApproveExpense(auth()->user(), $expense , $request->payment_method);
    }

    public function reject(RejectRequest $request, Expense $expense): JsonResponse
    {
        $data = $request->validated();
        $expenseStateService = new ExpenseStateService();

        $response = $expenseStateService->tryRejectExpense(
            auth()->user(), $expense, $data['rejection_comment']);


        return $response;
    }

    public function bulkApprove(ApproveRequest $request): JsonResponse
    {
        $expenseStateService = new ExpenseStateService();

        foreach ($request->ids as $id) {
            $expense = Expense::find($id);
            if (!$expense) continue;
            $response = $expenseStateService->tryApproveExpense(auth()->user(), $expense, $request->payment_method);
        }
        return $response ?? response()->json(['not found' , 404]);
    }


    public function bulkReject(Request $request): JsonResponse
    {
        $expenseStateService = new ExpenseStateService();
        foreach ($request->ids as $id) {
            $expense = Expense::find($id);
            if (!$expense) continue;

            $response = $expenseStateService->tryRejectExpense(
                auth()->user(), $expense, $request->rejection_comment);
        }

        return $response ?? response()->json(['not found' , 404]);
    }
    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        $expense = Expense::find($id);

        if (!$expense) {
            return response()->json(['message' => 'not found'], 404);
        }

        return response()->json($expense, 200);
    }

    public function destroy(string $id): JsonResponse
    {
        $expense = Expense::find($id);

        if (!$expense) {
            return response()->json(['message' => 'not found'], 404);
        }

        $expense->delete();

        return response()->json(['message' => 'destroyed successfully'], 200);
    }

    public function getVerifiedBySupervisorExpenses(): JsonResponse
    {
        $verifiedExpenses = Expense::query()
            ->where('state', VerifiedBySupervisor::$name)
            ->get();

        return response()->json(ExpenseResource::collection($verifiedExpenses));
    }

//
//    /**
//     * Update the specified resource in storage.
//     */
//    public function update(Request $request, string $id): JsonResponse
//    {
//        $expense = Expense::find($id);
//
//        if (!$expense) {
//            return response()->json(['message' => 'not found'], 404);
//        }
//
//        $validated = $request->validate([
//            'title' => 'sometimes|required|string|max:255',
//            'amount' => 'sometimes|required|numeric',
//        ]);
//
//        $expense->update($validated);
//
//        return response()->json([
//            'message' => 'updated successfully',
//            'data' => $expense
//        ], 200);
//    }
//
//    /**
//     * Remove the specified resource from storage.
//     */

}
