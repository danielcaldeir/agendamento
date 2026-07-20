<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Presenters\AppointmentPresenter;
use Illuminate\Database\Eloquent\Casts\Attribute;
// use Laracasts\Presenter\PresentableTrait;
use Illuminate\Database\Eloquent\SoftDeletes;

class Appointment extends Model
{
    use HasFactory;
    use SoftDeletes;
    // use PresentableTrait;

    // protected $presenter = AppointmentPresenter::class;
    public function presenter() {
        return new AppointmentPresenter($this);
    }

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'start_date',
        'end_date',
        'status',
        'color'
    ];

    // protected function status(): Attribute
    // {
    //     return Attribute::make(
    //         get: fn ($value) => $value / 100, // Recupera: divide por 100
    //         set: fn ($value) => $value * 100, // Salva: multiplica por 100
    //     );
    // }

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    /**
     * Get the appointment users.
     */
    // public function users()
    // {
    //     if ($this->type === 'patient') {
    //         return $this->hasMany(User::class, 'patient_id');
    //     }
    //     return $this->hasMany(User::class, 'doctor_id');
    // }

    /**
     * Get the patient.
     */
    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    /**
     * Get the doctor.
     */
    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

}
