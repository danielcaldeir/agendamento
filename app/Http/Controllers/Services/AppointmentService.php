<?php

namespace App\Http\Controllers\Services;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Responses\ServiceResponse;
use App\Models\Appointment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AppointmentService extends Controller
{
    // protected $appointmentService;
    protected $appointmentModel;
    protected $userModel;

    public function __construct() {
        // $this->appointmentService = $appointmentService;
        $this->appointmentModel = new Appointment();
        $this->userModel = new User();
    }

    public function getEndedAppointments(?int $userId = null, ?int $limit = null, bool $isCount = false)
    {
        if (is_null($userId)) {
            $user = auth()->user();
        } else {
            $user = $this->userModel->find($userId);
        }

        // $user = auth()->user();
        // $query = $this->appointmentModel
        //     ->where('status', 'confirmed')
        //     ->where('end_date', '<', Carbon::now()->toDateTimeString())
        //     ->orderBy('end_date', 'desc');

        if ($user->type === 'patient') {
            $patient = $this->userModel->find($userId);

            $query = $patient->appointments()
                ->where('status', 'confirmed')
                ->where('end_date', '<', Carbon::now()->toDateTimeString())
                ->orderBy('end_date', 'desc');

            $query->join('users', 'appointments.patient_id', '=', 'users.id');
        } else {
            if ($user->type === 'doctor') {
                $doctor = $this->userModel->find($userId);

                $query = $doctor->appointments()
                    ->where('status', 'confirmed')
                    ->where('end_date', '<', Carbon::now()->toDateTimeString())
                    ->orderBy('end_date', 'desc');

                $query->join('users', 'appointments.doctor_id', '=', 'users.id');
            } else {
                $query = $this->appointmentModel
                    ->where('status', 'confirmed')
                    ->where('end_date', '<', Carbon::now()->toDateTimeString())
                    ->orderBy('end_date', 'desc');
            }
            
        }
        
        // if (!is_null($userId)) {
        //     $query->where($user->type . '_id', $userId);
        // }

        // if (!is_null($userId)) {
        //     $user = $this->userModel->find($userId);
        //     if (!($user->type == 'admin')) {
        //         $query->where($user->type . '_id', $userId);
        //     }
        //     // $query->where($user->type . '_id', $userId);
        // }

        if ($isCount) {
            return $query->count();
        }

        if (!is_null($limit)) {
            $query = $query->limit(4);
        }

        // return $query->get();
        return $query->get(["appointments.id", "patient_id", "doctor_id", "start_date", "end_date", "status", "color"]);
    }

    public function getNextAppointmentDate()
    {
        $user = auth()->user();

        if ($user->type === 'admin') {
            $query = null;
        } else {
            $query = $this->appointmentModel
            ->where($user->type . '_id', $user->id)
            ->where('status', 'confirmed')
            // ->where('end_date', '>', Carbon::now()->toDateTimeString())
            ->orderBy('start_date', 'desc')
            ->first();
        }
        
        // $query = $this->userModel
        //     ->appointments()
        //     ->where('status', 'confirmed')
        //     // ->where('end_date', '>', Carbon::now()->toDateTimeString())
        //     ->orderBy('start_date')
        //     ->first();

        if (is_null($query)) {
            return '-';
        }

        return $query->start_date->format('d/m H:i');
    }

    public function getConfirmedAppointments(?int $userId = null, bool $isCount = false)
    {
        $query = $this->appointmentModel
            ->where('status', 'confirmed');

        if (!is_null($userId)) {
            $user = $this->userModel->find($userId);
            if (!($user->type == 'admin')) {
                $query->where($user->type . '_id', $userId);
            }
        }

        if ($isCount) {
            return $query->count();
        }

        return $query->orderBy('start_date', 'desc')->get();
    }

    public function getPendingAppointments()
    {
        $query = $this->appointmentModel
            ->where('status', 1)
            // ->where('end_date', '>', Carbon::now()->toDateTimeString())
            ->orderBy('start_date')
            ->get(['id', 'patient_id', 'doctor_id', 'start_date', 'end_date', 'status', 'color']);
        return $query;

        // $appointments = $this->appointmentModel
        //     ->where('status', 'pending')
        //     // ->where('end_date', '>', Carbon::now()->toDateTimeString())
        //     ->orderBy('start_date')
        //     ->get(['patient_id', 'doctor_id', 'start_date', 'end_date', 'status']);
        // return $appointments;
    }

    public function getNextAppointments(?int $limit = null)
    {
        $user = auth()->user();

        $query = $this->appointmentModel
            ->join('users', 'appointments.patient_id', '=', 'users.id')
            ->where('users.id', $user->id)
            ->whereNot('appointments.status', 'cancelled')
            ->where('end_date', '>', Carbon::now()->toDateTimeString())
            ->orderBy('start_date');

        // $query = $this->userModel
        //     ->appointments()
        //     ->where('users.id', $user->id)
        //     // ->where('end_date', '>', Carbon::now()->toDateTimeString())
        //     ->orderBy('start_date');

        if (!is_null($limit)) {
            $query = $query->limit(4);
        }

        return $query->get(['appointments.id', 'patient_id', 'doctor_id', 'start_date', 'end_date', 'status', 'color']);
        // return $query->get();
    }

    public function confirm(int $appointmentId): ServiceResponse
    {
        try {
            $user = auth()->user();

            if ($user->type !== 'admin') {
                return new ServiceResponse(
                    false,
                    'Usuário não tem permissão para confirmar agendamento'
                );
            }

            $appointment = $this->appointmentModel->find($appointmentId);
            $appointment->status = 'confirmed';
            $appointment->color = 'green';
            $appointment->save();
        } catch (\Throwable $th) {
            return new ServiceResponse(
                false,
                'Erro ao confirmar agendamento',
                null,
                $th
            );
        }

        return new ServiceResponse(
            true,
            'Agendamento confirmado com sucesso',
            $appointment
        );
    }

    public function cancel(int $appointmentId): ServiceResponse
    {
        try {
            $user = auth()->user();

            $query = $this->appointmentModel->where('id', $appointmentId);

            if ($user->type !== 'admin') {
                $query = $query->where(function ($query) use ($user) {
                    $query->where('patient_id', $user->id)
                        ->orWhere('doctor_id', $user->id);
                });
            }

            $appointment = $query->first();

            if (is_null($appointment)) {
                return new ServiceResponse(
                    false,
                    'Usuário não tem permissão para cancelar agendamento'
                );
            }

            $appointment->status = 'cancelled';
            $appointment->color = 'red';
            $appointment->save();
        } catch (\Throwable $th) {
            return new ServiceResponse(
                false,
                'Erro ao cancelar agendamento',
                null,
                $th
            );
        }

        return new ServiceResponse(
            true,
            'Agendamento cancelado com sucesso',
            $appointment
        );
    }

    private function loadAllPatientAppointments(
        int $patientId,
        ?string $startDate = null,
        ?string $endDate = null
    ) {
        $patient = $this->userModel->find($patientId);

        $query = $patient->appointments()
            ->where('status', '!=', 'cancelled');

        if (!is_null($startDate)) {
            $query = $query->where(
                'start_date',
                '>=',
                Carbon::parse($startDate)->toDateTimeString()
            );
        }

        if (!is_null($endDate)) {
            $query = $query->where(
                'end_date',
                '<=',
                Carbon::parse($endDate)->toDateTimeString()
            );
        }

        return $query
            ->join('users', 'users.id', 'appointments.doctor_id')
            ->get([
                'appointments.id',
                'users.name as title',
                'start_date as start',
                'end_date as end',
                DB::raw('CONCAT("bg-c-", color, " border-none") AS classNames'),
                DB::raw('CONCAT("/appointments/show/", appointments.id) AS url'),
            ]);
    }

    private function loadAllDoctorAppointments(
        int $doctorId,
        ?string $startDate = null,
        ?string $endDate = null
    ) {
        $doctor = $this->userModel->find($doctorId);

        $query = $doctor->appointments()->where('status', '!=', 'cancelled');

        if (!is_null($startDate)) {
            $query = $query->where(
                'start_date',
                '>=',
                Carbon::parse($startDate)->toDateTimeString()
            );
        }

        if (!is_null($endDate)) {
            $query = $query->where(
                'end_date',
                '<=',
                Carbon::parse($endDate)->toDateTimeString()
            );
        }

        return $query
            ->join('users', 'users.id', 'appointments.patient_id')
            ->get([
                'appointments.id',
                'users.name as title',
                'start_date as start',
                'end_date as end',
                DB::raw('CONCAT("bg-c-", color, " border-none") AS classNames'),
                DB::raw('CONCAT("/appointments/show/", appointments.id) AS url'),
            ]);
    }

    public function loadAllAppointments(?string $startDate = null, ?string $endDate = null): ServiceResponse
    {
        $user = auth()->user();

        if ($user->type !== 'admin') {
            // return null;
            return new ServiceResponse(
                false,
                'Erro ao realizar os agendamento',
                null, 
                $user->type
            );
        }

        $query = $this->appointmentModel->where('status', '!=', 'cancelled');

        if (!is_null($startDate)) {
            $query = $query->where(
                'start_date',
                '>=',
                Carbon::parse($startDate)->toDateTimeString()
            );
        }

        if (!is_null($endDate)) {
            $query = $query->where(
                'end_date',
                '<=',
                Carbon::parse($endDate)->toDateTimeString()
            );
        }

        // return $query
        //     // ->join('users', 'users.id', 'appointments.doctor_id')
        //     // ->where('users.id', $user->id)
        //     ->get([
        //         'appointments.id',
        //         // 'users.name as title',
        //         'start_date as start',
        //         'end_date as end',
        //         DB::raw('CONCAT("bg-c-", color, " border-none") AS classNames'),
        //         // DB::raw('CONCAT("/appointments/show/", appointments.id) AS url'),
        //     ]);

        $result = $query
            ->get([
                'appointments.id',
                // 'users.name as title',
                'start_date as start',
                'end_date as end',
                DB::raw('CONCAT("bg-c-", color, " border-none") AS classNames'),
                // DB::raw('CONCAT("/appointments/show/", appointments.id) AS url'),
            ]);
        
        return new ServiceResponse(
            true,
            'Todos os Agendamento realizados',
            $result
        );
    }

    public function loadAppointments(?string $startDate = null, ?string $endDate = null)
    {
        $user = auth()->user();

        if ($user->type === 'patient') {
            return $this->loadAllPatientAppointments($user->id, $startDate, $endDate);
        } else {
            if ($user->type === 'doctor') {
                return $this->loadAllDoctorAppointments($user->id, $startDate, $endDate);
            }
            return $this->loadAllAppointments($startDate, $endDate);
        }

        // $query = $this->appointmentModel
        //     ->where('status', '!=', 'cancelled');

        //// $query = $this->userModel->
        ////     appointments()
        ////     ->where('status', '!=', 'cancelled');

        // if (!is_null($startDate)) {
        //     $query = $query->where(
        //         'start_date',
        //         '>=',
        //         Carbon::parse($startDate)->toDateTimeString()
        //     );
        // }

        // if (!is_null($endDate)) {
        //     $query = $query->where(
        //         'end_date',
        //         '<=',
        //         Carbon::parse($endDate)->toDateTimeString()
        //     );
        // }

        // if ($user->type === 'patient') {
        //     return $query
        //         ->join('users', 'users.id', 'appointments.doctor_id')
        //         ->where('appointments.patient_id', $user->id)
        //         ->get([
        //             'appointments.id',
        //             'users.name as title',
        //             'start_date as start',
        //             'end_date as end',
        //             DB::raw('CONCAT("bg-c-", color, " border-none") AS classNames'),
        //             DB::raw('CONCAT("/appointments/show/", appointments.id) AS url'),
        //         ]);
        // }

        // return $query
        //     ->join('users', 'users.id', 'appointments.patient_id')
        //     ->where('appointments.doctor_id', $user->id)
        //     ->get([
        //         'appointments.id',
        //         'users.name as title',
        //         'start_date as start',
        //         'end_date as end',
        //         DB::raw('CONCAT("bg-c-", color, " border-none") AS classNames'),
        //         DB::raw('CONCAT("/appointments/show/", appointments.id) AS url'),
        //     ]);
    }

    public function loadDoctorAppointments(
        int $doctorId,
        ?string $startDate = null,
        ?string $endDate = null
    ) {
        $user = auth()->user();
        $doctor = $this->userModel
                            ->where('type', 'doctor')
                            ->find($doctorId);

        $query = $doctor->appointments()
                            ->where('status', '!=', 'cancelled');

        if (!is_null($startDate)) {
            $query = $query->where(
                'start_date',
                '>=',
                Carbon::parse($startDate)->toDateTimeString()
            );
        }

        if (!is_null($endDate)) {
            $query = $query->where(
                'end_date',
                '<=',
                Carbon::parse($endDate)->toDateTimeString()
            );
        }

        if ($user->type === 'admin'){
            return $query
                    ->join('users', 'users.id', 'appointments.patient_id')
                    ->get([
                        'appointments.id',
                        'users.name as title',
                        'start_date as start',
                        'end_date as end',
                        DB::raw('CONCAT("bg-c-", color, " border-none") AS classNames'),
                        DB::raw('CONCAT("/appointments/show/", appointments.id) AS url'),
                    ]);
        } else {
            return $query
                    ->get([
                        'appointments.id',
                        DB::raw('"Reservado" AS title'),
                        'start_date as start',
                        'end_date as end',
                        DB::raw('CONCAT("bg-c-gray border-none") AS classNames')
                    ]);
        }
        
    }

    public function loadPatientAppointments(
        int $patientId,
        ?string $startDate = null,
        ?string $endDate = null
    ) {
        $user = auth()->user();
        $patient = $this->userModel
                            ->where('type', 'patient')
                            ->find($patientId);

        if ($user->type === 'admin') {
            return $this->loadAllPatientAppointments($patient->id, $startDate, $endDate);
        } else {
            return abort(404);
        }
    }

    public function store(
        string $startDate,
        int $doctorId,
        ?int $patientId
    ):ServiceResponse {
        try {
            $user = auth()->user();

            $endDate = Carbon::parse($startDate)->addHours(1)->toDateTimeString();
            $startDate = Carbon::parse($startDate)->toDateTimeString();

            // $storeParams = new StoreServiceParams(
            //     $patientId ?? $user->id,
            //     $doctorId,
            //     $startDate,
            //     $endDate
            // );
            // $appointment = $this->appointmentModel->create($storeParams->toArray());

            $appointment = $this->appointmentModel->create([
                'patient_id' => $patientId,
                'doctor_id' => $doctorId,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'status' => 'pending',
                'color' => 'blue'
            ]);
        } catch (\Throwable $th) {
            return new ServiceResponse(
                false,
                'Erro ao criar agendamento',
                null,
                $th
            );
            // return response()->json([
            //     'success' => false,
            //     'message' => 'Erro ao criar agendamento!',
            //     'data' => null,
            //     'errors' => $th
            // ]);
        }
        return new ServiceResponse(
            true,
            'Agendamento criado com sucesso',
            $appointment
        );
        // return response()->json([
        //     'success' => true,
        //     'message' => 'Agendamento criado com sucesso!',
        //     'data' => $appointment
        // ]);
    }

    public function destroy(string $appointmentId):ServiceResponse
    {
        try {
            $user = auth()->user();

            $query = $this->appointmentModel->where('id', $appointmentId);

            if ($user->type !== 'admin') {
                $query = $query->where(function ($query) use ($user) {
                    $query->where('patient_id', $user->id)
                        ->orWhere('doctor_id', $user->id);
                });
            }

            $appointment = $query->first();

            if (is_null($appointment)) {
                return new ServiceResponse(
                    false,
                    'Usuário não tem permissão para deletar agendamento'
                );
            }

            $appointment->delete();
        } catch (\Throwable $th) {
            return new ServiceResponse(
                false,
                'Erro ao deletar agendamento',
                null,
                $th
            );
        }

        return new ServiceResponse(
            true,
            'Agendamento deletar com sucesso',
            $appointment
        );
    }

}
