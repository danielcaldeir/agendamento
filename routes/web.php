<?php

use App\Http\Controllers\AppointmentController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use App\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Route::get('/', function () {
//     return view('index');
// });

Route::get('/', [HomeController::class, 'index'] )->name('index');
Route::get('/signin', [HomeController::class, 'signin'] )->name('login');
Route::get('/signup', [HomeController::class, 'signup'] )->name('register');
Route::post('/signin', [AuthController::class, 'signin'])->name('signin');
Route::post('/signup', [AuthController::class, 'signup'])->name('signup');
// Route::post('/signup', [PostController::class, 'signup'])->name('signup');
Route::get('/logout',  [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('users', UserController::class)->only(['update', 'edit']);
    Route::get('/appointments/load', [AppointmentController::class, 'load'])->name('appointments.load');
    Route::get('/appointments/load-all', [AppointmentController::class, 'loadAll'])->name('appointments.load-all');
    Route::get('/appointments/doctor/load', [AppointmentController::class, 'loadDoctor'])->name('appointments.doctor.load');
    Route::get('/appointments/patient/load', [AppointmentController::class, 'loadPatient'])->name('appointments.patient.load');
    Route::get('/appointments/confirm/{id}', [AppointmentController::class, 'confirm'])->name('appointments.confirm');
    Route::get('/appointments/cancel/{id}', [AppointmentController::class, 'cancel'])->name('appointments.cancel');
    Route::get('/appointments/index', [AppointmentController::class, 'index'])->name('appointments.index');
    Route::get('/appointments/store', [AppointmentController::class, 'store'])->name('appointments.store');
    Route::post('/appointments/store', [AppointmentController::class, 'store']);
    Route::get('/appointments/destroy/{id}', [AppointmentController::class, 'destroy'])->name('appointments.destroy');
    Route::get('/appointments/show/{id}', [AppointmentController::class, 'show'])->name('appointments.show');
    // Route::resource('appointments', [AppointmentController::class]);
    // Route::resource('appointments', AppointmentController::class);
    // Route::resource('users', 'UserController')->only(['update', 'edit']);
    // Route::get('/appointments/load', 'AppointmentController@load')->name('appointments.load');
    // Route::get('/appointments/load-all', 'AppointmentController@loadAll')->name('appointments.load-all');
    // Route::get('/appointments/doctor/load', 'AppointmentController@loadDoctor')->name('appointments.doctor.load');
    // Route::get('/appointments/confirm/{id}', 'AppointmentController@confirm')->name('appointments.confirm');
    // Route::get('/appointments/cancel/{id}', 'AppointmentController@cancel')->name('appointments.cancel');
    // Route::resource('appointments', 'AppointmentController');
    Route::resource('doctors', DoctorController::class)->only(['update', 'edit']);
    // Route::post('/doctors/update', [DoctorController::class, 'update'])->name('doctors.update');
    Route::get('/doctors/available', [DoctorController::class, 'getAvailableByDate'])->name('doctors.available');
    Route::get('/doctors/index', [DoctorController::class, 'index'])->name('doctors.index');
    Route::get('/doctors/create', [DoctorController::class, 'create'])->name('doctors.create');
    Route::get('/doctors/show/{id}', [DoctorController::class, 'show'])->name('doctors.show');
    Route::post('/doctors/store', [DoctorController::class, 'store'])->name('doctors.store');
    Route::post('/doctors/destroy', [DoctorController::class, 'destroy'])->name('doctors.destroy');
    // Route::resource('doctors', [DoctorController::class ]);
    Route::get('/patients/available', [PatientController::class, 'getAvailableByDate'])->name('patients.available');
    // Route::get('/patients/create',    [PatientController::class, 'create'])->name('patients.create');
    Route::get('/patients/index',     [PatientController::class, 'index'] )->name('patients.index');
    Route::resource('patients', PatientController::class)->only(['create', 'edit']);
    Route::get('/patients/show/{id}', [PatientController::class, 'show']  )->name('patients.show');
    Route::post('/patients/update/{id}', [PatientController::class, 'update'])->name('patients.update');
    Route::post('/patients/store', [PatientController::class, 'store'])->name('patients.store');
    Route::post('/patients/destroy', [PatientController::class, 'destroy'])->name('patients.destroy');
    // Route::resource('patients', [PatientController::class ]);
    // Route::resource('admins', [UserController::class ]);
    Route::get('/admins/index', [UserController::class, 'index'])->name('admins.index');
    Route::get('/admins/create', [UserController::class, 'create'])->name('admins.create');
    Route::get('/admins/show/{id}', [UserController::class, 'show'])->name('admins.show');
    Route::post('/admins/store', [PatientController::class, 'store'])->name('admins.store');
    Route::post('/admins/destroy/{id}', [UserController::class, 'destroy'])->name('admins.destroy');
    Route::get('/users/history', [UserController::class, 'history'])->name('users.history');
    // Route::get('/doctors/available', 'DoctorController@getAvailableByDate')->name('doctors.available');
    // Route::resource('doctors', 'DoctorController');
    // Route::get('/patients/available', 'PatientController@getAvailableByDate')->name('patients.available');
    // Route::resource('patients', 'PatientController');
    // Route::resource('admins', 'UserController');
    // Route::get('/users/history', 'UserController@history')->name('users.history');
});




Route::get('posts/create', [PostController::class, 'store']);
Route::get('posts/all',    [PostController::class, 'index']);
Route::get('posts/read',   [PostController::class, 'show']);
Route::get('posts/update', [PostController::class, 'update']);
Route::get('posts/delete', [PostController::class, 'destroy']);

Route::post('posts/signin', [UserController::class, 'signin'])->withoutMiddleware(VerifyCsrfToken::class);
Route::post('posts/signup', [UserController::class, 'signup']);
Route::post('posts/me',     [UserController::class, 'me']);
