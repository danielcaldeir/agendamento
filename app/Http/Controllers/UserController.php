<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Services\UserService;
use App\Http\Requests\CreateUserRequest;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    protected $userModel;
    protected $userService;

    public function __construct()
    {
        $this->userModel = new User();
        $this->userService = new UserService();
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = auth()->user();
        $admins = User::where('type', 'admin')->orderBy('name')->get();

        return view('admin.admins', compact('user', 'admins'));
    }

    /**
     * Show the form for creating the resource.
     */
    public function signin(Request $request): JsonResponse
    {
        $headers = $request->headers->getIterator();
        return response()->json(['method'=>'signin']);
    }

    /**
     * Store the newly created resource in storage.
     */
    public function signup(CreateUserRequest $request): JsonResponse
    {
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
        // return response()->json(['method'=>'signup']);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user = auth()->user();

        if ($user->type === 'admin') {
            return view('admin.admin-create', compact('user'));
        }

        abort(404);
    }

    /**
     * Display the resource.
     */
    public function me(Request $request): JsonResponse
    {
        $headers = $request->headers->getIterator();
        return response()->json(['method'=>'me'], 200, $headers->getArrayCopy());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = auth()->user();

        if ($user->type !== 'admin') {
            return redirect(route('admins.index'))->withError('Usuário sem permissões!');
        }

        $storeResponse = $this->userService->store($request, 'admin');

        if (!$storeResponse->getSuccess()) {
            return redirect(route('admins.index'))->withError('Erro ao criar usuário!');
        }

        return redirect(route('admins.index'))->withSuccess('Usuário criado com sucesso!');
    }

    /**
     * Display specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        $user = auth()->user();

        $admin = $this->userModel->find($id);

        return view('admin.admin', compact('user', 'admin'));
    }

    /**
     * Show the form for editing the resource.
     */
    public function edit()
    {
        $user = auth()->user();
        return view('config', compact('user'));
    }

    /**
     * Update the resource in storage.
     */
    public function update(Request $request)
    {
        $user = auth()->user();

        $updateResponse = $this->userService->update($request, $user->id);

        if (!$updateResponse->getSuccess()) {
            return redirect(route('users.edit', $user->id))->withError('Erro ao editar usuário!');
        }

        return redirect(route('users.edit', $user->id))->withSuccess('Usuário editado com sucesso!');
    }

    /**
     * Remove the resource from storage.
     */
    public function destroy(int $id)
    {
        $user = auth()->user();

        if ($user->type !== 'admin') {
            return redirect(route('admins.index'))->withError('Usuário sem permissões!');
        }

        $destroyResponse = $this->userService->destroy($id);

        if (!$destroyResponse->getSuccess()) {
            dd($destroyResponse->getErrors());
            return redirect(route('admins.index'))->withError('Erro ao remover usuário!');
        }

        return redirect(route('admins.index'))->withSuccess('Usuário removido com sucesso!');
    }
}
