<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function show()
    {
        return view('admin.login');
    }

    public function login(Request $request)
    {
        $request->validate(['password' => ['required', 'string']]);

        $input   = (string) $request->input('password');
        $envPass = (string) env('RANCHO_ORG_PASSWORD', '');

        if ($envPass === '') {
            return back()->with('error', 'Senha do admin não configurada (.env).');
        }

        $ok = false;
        if (str_starts_with($envPass, '$2y$')) {
            // bcrypt no .env
            $ok = password_verify($input, $envPass);
        } else {
            // texto puro no .env
            $ok = hash_equals($envPass, $input);
        }

        if (!$ok) {
            return back()->with('error', 'Senha inválida.');
        }

        $request->session()->put('org.auth', true);

        // Vai para a lista de inscrições
        return redirect()->route('admin.reg.index');
    }

    public function logout(Request $request)
    {
        $request->session()->forget('org.auth');
        return redirect()->route('admin.login')->with('ok', 'Você saiu do painel.');
    }
}
