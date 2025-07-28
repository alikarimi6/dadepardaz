<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;

class RegisterController extends Controller
{

    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        try {
            $client = new Client();
//            todo: resolve api server
            $response = $client->post('http://localhost:8001/api/v1/auth/register', [
                'form_params' => $request->all(),
                'headers' => [
                    'Accept' => 'application/json',
                ],
            ]);
            $data = json_decode($response->getBody()->getContents(), true);
            return redirect()->route('login')->with('success', 'ثبت‌نام با موفقیت انجام شد.');

        } catch (RequestException $e) {
            $errorResponse = $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : null;
            $errorData = $errorResponse ? json_decode($errorResponse, true) : null;
            if ($errorData && isset($errorData['errors'])) {
                $errors = [];
                foreach ($errorData['errors'] as $field => $messages) {
                    $errors[$field] = is_array($messages) ? $messages[0] : $messages;
                }
                return back()->withErrors($errors)->withInput();
            }
        }
    }
}
