<?php

namespace App\Http\Controllers\Api\V1\Iban;



use App\Http\Requests\Iban\IbanStoreRequest;
use Faker\Calculator\Iban;

class IbanController
{
    public function store(IbanStoreRequest $request)
    {
        $data = $request->validated();
        $user = $request->user();
        $iban = $user->ibans()->create([
            'iban' => $data['iban'],
        ]);
        return response()->json(['message' => 'iban added successfully' , 'iban' => $iban]);
    }
}
