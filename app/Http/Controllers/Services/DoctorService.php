<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Responses\ServiceResponse;
use App\Models\Doctor;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DoctorService extends Controller
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

    /**
     * Display a listing of the resource.
     */
    public function findAllDoctors(): ServiceResponse
    {
        try {
            // $user = $this->userModel->find($userId);
            $doctors = $this->userModel
                ->where('type', 'doctor')
                ->orderBy('name')
                ->get();
            
            return new ServiceResponse(
                true,
                'Consulta realizada com sucesso!',
                $doctors
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
    public function findDoctorId(string $userId): ServiceResponse
    {
        try {
            // $user = $this->userModel->find($userId);
            $patients = $this->userModel
                ->where('type', 'doctor')
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

            $doctors = $this->userModel
                ->where('users.type', 'doctor')
                ->whereNotIn('users.id', function ($query) use ($date) {
                    $query->from('appointments')
                        ->select('appointments.doctor_id')
                        ->where('appointments.start_date', $date)
                        ->where('status', '!=', 'cancelled')
                        ->whereNull('deleted_at');
                })
                ->leftJoin('doctors', 'users.id', 'doctors.user_id')
                ->get(['users.id', 'users.name', 'users.image', 'doctors.specialty']);
        } catch (\Throwable $th) {
            return new ServiceResponse(
                false,
                'Erro ao buscar médicos!',
                null,
                $th
            );
        }

        return new ServiceResponse(
            true,
            'Busca por médicos com sucesso!',
            $doctors
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

            $doctor = Doctor::create([
                'user_id' => $request->user_id,
                'doctor' => $request->doctor,
                'birth_date' => $request->birth_date,
            ]);

            return new ServiceResponse(
                true,
                'Doctor criado com sucesso!',
                $doctor
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
                'Erro ao criar doctor!',
                null,
                $th
            );
        }
    }
}
