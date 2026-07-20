<?php

namespace App\Presenters;

// use Laracasts\Presenter\Presenter;

class AppointmentPresenter 
{
    protected $appointment;

    public function __construct($appointment){
        $this->appointment = $appointment;
    }
    
    public function status(): string
    {
        if ($this->appointment->status === 'pending') {
            return 'Pendente';
        }
        if ($this->appointment->status === 'confirmed') {
            return 'Confirmado';
        }
        return 'Cancelado';
    }
}
