<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\User;
use App\Http\Requests\StorePatientRequest;
use App\Http\Requests\UpdatePatientRequest;
use App\Http\Controllers\Services\UserService;
use Illuminate\Http\Request;

use App\Http\Controllers\Services\PatientService;

class PatientController extends Controller
{
    protected $userModel;
    protected $userService;
    protected $patientService;

    public function __construct() {
        $this->userModel = new User();
        $this->userService = new UserService();
        $this->patientService = new PatientService();
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();

        $patients = $this->userModel
            ->where('type', 'patient')
            ->orderBy('name')
            ->get();
        // $patients = User::where('type', 'patient')->orderBy('name')->get();

        return view('admin.patients', compact('user', 'patients'));
    }

    public function getAvailableByDate(Request $request)
    {
        $serviceResponse = $this->patientService->getAvailableByDate(
            $request->date
        );

        if (!$serviceResponse->getSuccess()) {
            return response()->json($serviceResponse->getErrors());
        }

        return response()->json($serviceResponse->getData());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $user = auth()->user();

        if ($user->type === 'admin') {
            return view('admin.patient-create', compact('user'));
        }

        abort(404);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePatientRequest $request)
    {
        $user = auth()->user();

        if ($user->type !== 'admin') {
            return redirect(route('patients.index'))->withError('Usuário sem permissões!');
        }

        $storeResponse = $this->userService->store($request, 'patient');

        if (!$storeResponse->getSuccess()) {
            return redirect(route('patients.index'))->withError('Erro ao criar usuário!');
        }

        return redirect(route('patients.index'))->withSuccess('Usuário criado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $user = auth()->user();

        $patient = $this->userModel->find($id);

        return view('admin.patient', compact('user', 'patient'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id)
    {
        $user = auth()->user();

        $showResponse = $this->patientService->findPatientId($id);

        if (!$showResponse->getSuccess()) {
            $patient = $showResponse->getErrors();
            return view('patient', compact('user', 'patient'));
        } else {
            $patient = $showResponse->getData();
        }

        if ($user->type === 'admin') {
            return view('admin.patient-edit', compact('user', 'patient'));
        }

        abort(404);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id)
    {
        $updateResponse = $this->userService->update($request, $id);

        if (!$updateResponse->getSuccess()) {
            return redirect(route('patients.show', $id))->withError('Erro ao editar usuário!');
        }

        return redirect(route('patients.show', $id))->withSuccess('Usuário editado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Patient $patient)
    {
        $user = auth()->user();

        if ($user->type !== 'admin') {
            return redirect(route('patients.index'))->withError('Usuário sem permissões!');
        }

        $destroyResponse = $this->userService->destroy($patient->id);

        if (!$destroyResponse->getSuccess()) {
            return redirect(route('patients.index'))->withError('Erro ao remover usuário!');
        }

        return redirect(route('patients.index'))->withSuccess('Usuário removido com sucesso!');
    }
}
