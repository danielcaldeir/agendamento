<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Services\DoctorService;
use App\Http\Controllers\Services\UserService;
// use App\Models\Doctor;
use App\Http\Requests\StoreDoctorRequest;
use App\Http\Requests\UpdateDoctorRequest;
// use App\Models\User;
use Illuminate\Http\Request;

class DoctorController extends Controller
{
    // protected $userModel;
    protected $userService;
    protected $doctorService;

    public function __construct()
    {
        $this->userService = new UserService();
        $this->doctorService = new DoctorService();
        // $this->userModel = new User();
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();

        // $doctors = $this->userModel
        //     ->where('type', 'doctor')
        //     ->orderBy('name')
        //     ->get();
        // $doctors = User::where('type', 'doctor')->orderBy('name')->get();

        $serviceResponse = $this->doctorService->findAllDoctors();

        if (!$serviceResponse->getSuccess()) {
            $doctors = $serviceResponse->getErrors();
            return view('admin.doctors', compact('user', 'doctors'));
        } else {
            $doctors = $serviceResponse->getData();
        }

        return view('admin.doctors', compact('user', 'doctors'));
    }

    public function getAvailableByDate(Request $request)
    {
        $serviceResponse = $this->doctorService->getAvailableByDate(
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
            return view('admin.doctor-create', compact('user'));
        }

        abort(404);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDoctorRequest $request)
    {
        $user = auth()->user();

        if ($user->type !== 'admin') {
            return redirect(route('doctors.index'))->withError('Usuário sem permissões!');
        }

        $storeResponse = $this->userService->store($request, 'doctor');

        if (!$storeResponse->getSuccess()) {
            return redirect(route('doctors.index'))->withError('Erro ao criar usuário!');
        }

        return redirect(route('doctors.index'))->withSuccess('Usuário criado com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {
        $user = auth()->user();

        // $doctor = $this->userModel->find($id);

        $showResponse = $this->userService->show($id);

        if (!$showResponse->getSuccess()) {
            $doctor = $showResponse->getErrors();
            return view('doctor', compact('user', 'doctor'));
        } else {
            $doctor = $showResponse->getData();
        }

        if ($user->type === 'admin') {
            return view('admin.doctor', compact('user', 'doctor'));
        }

        return view('doctor', compact('user', 'doctor'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id)
    {
        $user = auth()->user();

        $showResponse = $this->doctorService->findDoctorId($id);

        if (!$showResponse->getSuccess()) {
            $doctor = $showResponse->getErrors();
            return view('doctor', compact('user', 'doctor'));
        } else {
            $doctor = $showResponse->getData();
        }

        if ($user->type === 'admin') {
            return view('admin.doctor-edit', compact('user', 'doctor'));
        }

        abort(404);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDoctorRequest $request, string $id)
    {
        $updateResponse = $this->userService->update($request, $id);

        if (!$updateResponse->getSuccess()) {
            return redirect(route('doctors.show', $id))->withError('Erro ao editar usuário!');
        }

        return redirect(route('doctors.show', $id))->withSuccess('Usuário editado com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = auth()->user();

        if ($user->type !== 'admin') {
            return redirect(route('doctors.index'))->withError('Usuário sem permissões!');
        }

        $destroyResponse = $this->userService->destroy($id);

        if (!$destroyResponse->getSuccess()) {
            return redirect(route('doctors.index'))->withError('Erro ao remover usuário!');
        }

        return redirect(route('doctors.index'))->withSuccess('Usuário removido com sucesso!');
    }
}
