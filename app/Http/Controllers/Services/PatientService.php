<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Responses\ServiceResponse;
use App\Models\Patient;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PatientService extends Controller
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    /**
     * Display a listing of the resource.
     */
    public function findAllPatients(): ServiceResponse
    {
        try {
            // $user = $this->userModel->find($userId);
            $patients = $this->userModel
                ->where('type', 'patient')
                ->orderBy('name')
                ->get();
            
            return new ServiceResponse(
                true,
                'Consulta realizada com sucesso!',
                $patients
            );
        } catch (\Throwable $th) {
            return new ServiceResponse(
                false,
                'Erro ao realizar a Consulta!',
                null,
                $th
            );
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function findPatientId(string $userId): ServiceResponse
    {
        try {
            // $user = $this->userModel->find($userId);
            $patients = $this->userModel
                ->where('type', 'patient')
                ->find($userId);
            
            return new ServiceResponse(
                true,
                'Consulta realizada com sucesso!',
                $patients
            );
        } catch (\Throwable $th) {
            return new ServiceResponse(
                false,
                'Erro ao realizar a Consulta!',
                null,
                $th
            );
        }
    }

    public function getAvailableByDate(string $date): ServiceResponse
    {
        try {
            $date = Carbon::parse($date)->toDateTimeString();

            $patients = $this->userModel
                ->where('users.type', 'patient')
                ->whereNotIn('users.id', function ($query) use ($date) {
                    $query->from('appointments')
                        ->select('appointments.patient_id')
                        ->where('appointments.start_date', $date)
                        ->where('status', '!=', 'cancelled');
                })
                ->leftJoin('patients', 'users.id', 'patients.user_id')
                ->get(['users.id', 'users.name', 'users.image', 'patients.blood_type']);
        } catch (\Throwable $th) {
            return new ServiceResponse(
                false,
                'Erro ao buscar pacientes!',
                null,
                $th
            );
        }

        return new ServiceResponse(
            true,
            'Busca por pacientes com sucesso!',
            $patients
        );
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            // $user = User::create([
            //     'name'      => $request->name,
            //     'email'     => $request->email,
            //     'type'      => $type,
            //     'password'  => Hash::make($request->password)
            // ]);

            $patient = Patient::create([
                'user_id' => $request->user_id,
                'blood_type' => $request->blood_type,
                'social_number' => $request->social_number,
            ]);

            return new ServiceResponse(
                true,
                'Patient criado com sucesso!',
                $patient
            );

            // if ($type === 'patient') {
            //     Patient::create([
            //         'user_id' => $user->id
            //     ]);
            //     $user->refresh();
            //     $patient = $user->patient;
            //     $patient->blood_type = $request->blood;
            //     $patient->social_number = $request->social;
            //     $patient->save();
            // }
            // if ($type === 'doctor') {
            //     Doctor::create([
            //         'user_id' => $user->id
            //     ]);
            //     $user->refresh();
            //     $doctor = $user->doctor;
            //     $doctor->specialty = $request->specialty;
            //     $doctor->save();
            // }
            // if ($type === 'admin') {
            //     $user->api_token = Str::random(80);
            //     $user->save();
            // }
        } catch (\Throwable $th) {
            return new ServiceResponse(
                false,
                'Erro ao criar patient!',
                null,
                $th
            );
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
