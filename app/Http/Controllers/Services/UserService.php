<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Responses\ServiceResponse;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserService extends Controller
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }
    /**
     * Display a listing of the resource.
     */
    // public function index()
    // {
    //     //
    // }

    /**
     * Show the form for creating a new resource.
     */
    // public function create()
    // {
    //     //
    // }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, string $type): ServiceResponse
    {
        try {
            $user = User::create([
                'name'      => $request->name,
                'email'     => $request->email,
                'type'      => $type,
                'password'  => Hash::make($request->password)
            ]);

            // Verifica se informou o arquivo e se é válido
            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                // Define um aleatório para o arquivo baseado no timestamps atual
                $name = Str::random(6);
                $extension = $request->image->extension();
                $nameFile = "{$name}.{$extension}";

                // Faz o upload:
                $upload = $request->image->storeAs('img/pictures', $nameFile);

                // Verifica se NÃO deu certo o upload (Redireciona de volta)
                if (!$upload) {
                    return redirect()
                        ->back()
                        ->withError('Falha ao fazer upload da imagem')
                        ->withInput();
                }

                $user->image = $nameFile;
                $user->save();
            }

            if ($type === 'patient') {
                Patient::create([
                    'user_id' => $user->id
                ]);
                $user->refresh();

                $patient = $user->patient;
                $patient->blood_type = $request->blood;
                $patient->social_number = $request->social;
                $patient->save();
            }

            if ($type === 'doctor') {
                Doctor::create([
                    'user_id' => $user->id
                ]);
                $user->refresh();

                $doctor = $user->doctor;
                $doctor->specialty = $request->specialty;
                $doctor->save();
            }

            if ($type === 'admin') {
                $user->api_token = Str::random(80);
                $user->save();
            }
        } catch (\Throwable $th) {
            return new ServiceResponse(
                false,
                'Erro ao editar usuário!',
                null,
                $th
            );
        }

        return new ServiceResponse(
            true,
            'Usuário editado com sucesso!',
            $user
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(string $userId): ServiceResponse
    {
        try {
            $user = $this->userModel->find($userId);
            
            return new ServiceResponse(
                true,
                'Usuário encontrado com sucesso!',
                $user
            );
        } catch (\Throwable $th) {
            return new ServiceResponse(
                false,
                'Erro ao encontrar usuário!',
                null,
                $th
            );
        }
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
    public function update(Request $request, string $userId): ServiceResponse
    {
        try {
            $user = $this->userModel->find($userId);

            if ($user->type === 'patient' && is_null($user->patient)) {
                Patient::create([
                    'user_id' => $user->id
                ]);
                $user->refresh();
            }

            if ($user->type === 'doctor' && is_null($user->doctor)) {
                Doctor::create([
                    'user_id' => $user->id
                ]);
                $user->refresh();
            }

            // Verifica se informou o arquivo e se é válido
            if ($request->hasFile('image') && $request->file('image')->isValid()) {
                // Define um aleatório para o arquivo baseado no timestamps atual
                $name = Str::random(6);
                $extension = $request->image->extension();
                $nameFile = "{$name}.{$extension}";

                // Faz o upload:
                $upload = $request->image->storeAs('img/pictures', $nameFile);

                // Verifica se NÃO deu certo o upload (Redireciona de volta)
                if (!$upload) {
                    return redirect()
                        ->back()
                        ->withError('Falha ao fazer upload da imagem')
                        ->withInput();
                }
                $user->image = $nameFile;
            }

            if ($user->type === 'patient') {
                $patient = $user->patient;
                $patient->blood_type = $request->blood;
                $patient->social_number = $request->social;
                $patient->save();
            }

            if ($user->type === 'doctor') {
                $doctor = $user->doctor;
                $doctor->specialty = $request->specialty;
                $doctor->save();
            }

            $user->name = $request->name;
            $user->save();
        } catch (\Throwable $th) {
            return new ServiceResponse(
                false,
                'Erro ao editar usuário!',
                null,
                $th
            );
            // return response()->json([
            //     'success' => false,
            //     'message' => 'Erro ao editar usuário!',
            //     'data' => null,
            //     'errors' => $th
            // ]);
        }

        return new ServiceResponse(
            true,
            'Usuário editado com sucesso!',
            $user
        );
        // return response()->json([
        //     'success' => true,
        //     'message' => 'Usuário editado com sucesso!',
        //     'data' => $user
        // ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $userId): ServiceResponse
    {
        try {
            $user = $this->userModel->find($userId);
            $user->delete();
        } catch (\Throwable $th) {
            return new ServiceResponse(
                false,
                'Erro ao remover usuário!',
                null,
                $th
            );
        }

        return new ServiceResponse(
            true,
            'Usuário removido com sucesso!',
            $user
        );
    }
}
