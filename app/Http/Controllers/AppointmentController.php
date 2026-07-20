<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Services\AppointmentService;
use App\Http\Requests\AppointmentRequest;
use Carbon\Carbon;
use App\Models\Appointment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AppointmentController extends Controller
{
    protected $appointmentService;
    protected $appointmentModel;
    protected $userModel;

    public function __construct(
        // AppointmentService $appointmentService,
        // Appointment $appointmentModel
    ) {
        $this->appointmentService = new AppointmentService();
        $this->appointmentModel = new Appointment();
        $this->userModel = new User();
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        if ($user->type === 'admin') {
            return view('admin.appointments', compact('user'));
        }
        return view('appointments', compact('user'));
    }

    /**
     * Show the form for creating a new resource.
     */
    // public function create()
    // {
    //     //
    // }

    public function load(Request $request)
    {
        $appointments = $this->appointmentService->loadAppointments(
            $request->start,
            $request->end
        );
        return response()->json($appointments);
    }

    public function loadAll(Request $request)
    {
        $loadResponse = $this->appointmentService->loadAllAppointments(
            $request->start,
            $request->end
        );
        if ($loadResponse->getSuccess()) {
            $appointments = $loadResponse->getData();
        } else {
            return response()->json($loadResponse);
        }
        // $appointments = $this->appointmentService->loadAllAppointments(
        //     $request->start,
        //     $request->end
        // );

        return response()->json($appointments);
    }

    /**
     * Store a newly created resource in storage.
     */
    // public function store(Request $request)
    public function store(AppointmentRequest $request)
    {
        $storeReponse = $this->appointmentService->store($request->date, $request->doctor, $request->patient);

        // if (!$storeReponse->getStatusCode()) {
        if (!$storeReponse->getSuccess()) {
            return redirect()->back()->withError('Erro ao agendar consulta');
        }

        return redirect()->back()->withSuccess('Consulta agendada!');
    }

    public function loadDoctor(Request $request)
    {
        $appointments = $this->appointmentService->loadDoctorAppointments(
            $request->id,
            $request->start,
            $request->end
        );

        return response()->json($appointments);
    }

    public function loadPatient(Request $request)
    {
        $appointments = $this->appointmentService->loadPatientAppointments(
            $request->id,
            $request->start,
            $request->end
        );

        return response()->json($appointments);
    }

    public function getDoctorsAvailableByDate(Request $request)
    {
        $appointments = $this->appointmentService->getDoctorsAvailableByDate(
            $request->date
        );

        return response()->json($appointments);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = auth()->user();

        $query = $this->appointmentModel->where('id', $id);

        if ($user->type === 'admin') {
            $appointment = $query->first();

            return view('admin.appointmentView', compact('user', 'appointment'));
        }

        $appointment = $query
            ->where(function ($query) use ($user) {
                $query->where('patient_id', $user->id)
                    ->orWhere('doctor_id', $user->id);
            })
            ->first();

        if (is_null($appointment)) {
            abort(404);
        }

        return view('appointmentView', compact('user', 'appointment'));
    }

    /**
     * Cancel the appointment.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function cancel(int $id)
    {
        $cancelReponse = $this->appointmentService->cancel($id);

        if (!$cancelReponse->getSuccess()) {
            return redirect()->route('appointments.index')->withError('Erro ao cancelar consulta');
        }

        return redirect()->route('appointments.index')->withSuccess('Consulta cancelada!');
    }

    /**
     * Confirm the appointment.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function confirm(int $id)
    {
        $confirmReponse = $this->appointmentService->confirm($id);
        
        if (!$confirmReponse->getSuccess()) {
            return redirect()->route('dashboard')->withError('Erro ao confirmar consulta');
        }

        return redirect()->route('dashboard')->withSuccess('Consulta confirmada!');
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
    public function destroy(string $id)
    {
        $destroyReponse = $this->appointmentService->destroy($id);

        if (!$destroyReponse->getSuccess()) {
            return redirect()->route('appointments.index')->withError('Erro ao deletar consulta');
        }

        return redirect()->route('appointments.index')->withSuccess('Consulta deletada!');
    }

}
