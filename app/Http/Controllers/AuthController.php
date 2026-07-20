<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserRequest;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
    /**
     * Efetuar o Logout da Autenticacao.
     */
    public function logout()
    {
        Session::flush();
        Auth::logout();
        return redirect()->route('login');
    }

    /**
     * Realizar o Registro no Sistema.
     */
    public function signup(CreateUserRequest $request)
    {
        // request()->validate([
        //     'name'     => 'required',
        //     'email'    => 'required|email|unique:users',
        //     'password' => 'required|confirmed|min:6',
        // ]);
        $data = $request->all();
        $user = User::create([
            'name'      => $data['name'],
            'email'     => $data['email'],
            'password'  => Hash::make($data['password'])
        ]);
        Patient::create([
            'user_id' => $user->id
        ]);
        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            $user = auth()->user();
            return redirect('/dashboard')->withSuccess('Usuário criado com sucesso!');
        }
        return redirect()->back()->withError('Erro ao criar o usuário!');
    }

    /**
     * Fazer o acesso ao Sistema Login.
     */
    public function signin(Request $request)
    {
        request()->validate([
            'email'    => 'required',
            'password' => 'required',
        ]);
        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            $user = auth()->user();
            return redirect('/dashboard')->withSuccess('Bem-vindo, ' . $user->name . '!');
        }
        return redirect()->back()->withError('Ops! Login incorreto.');
    }

    /**
     * Display AppLogin specified resource.
     */
    public function makeAppLogin(Request $request)
    {
        $credentials = $request->only('email', 'password');
        if (Auth::attempt($credentials)) {
            $user = auth()->user();
            return response()->json([
                'success'   => true,
                'api_token' => $user->api_token
            ]);
        }
        return response()->json(['success' => false]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    // public function edit(string $id)
    // {
    //     //
    // }

    /**
     * Update the specified resource in storage.
     */
    // public function update(Request $request, string $id)
    // {
    //     //
    // }

    /**
     * Remove the specified resource from storage.
     */
    // public function destroy(string $id)
    // {
    //     //
    // }
}
