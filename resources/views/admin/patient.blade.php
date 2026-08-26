@extends('layouts.app')

@section('title', '| Paciente')
@section('sidebar_patients', 'active')

@section('content')
    <!-- [ Main Content ] start -->
    <div class="pcoded-main-container">
        <div class="pcoded-wrapper">
            <div class="pcoded-content">
                <div class="pcoded-inner-content">
                    <!-- [ breadcrumb ] start -->
                    <div class="page-header">
                        <div class="page-block">
                            <div class="row align-items-center">
                                <div class="col-md-12">
                                    <div class="page-header-title">
                                        <h5 class="m-b-10">Paciente</h5>
                                    </div>
                                    <ul class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="feather icon-home"></i></a></li>
                                        <li class="breadcrumb-item"><a href="{{ route('patients.index') }}">Pacientes</a></li>
                                        <li class="breadcrumb-item"><a href="javascript:">Paciente</a></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- [ breadcrumb ] end -->
                    <div class="main-body">
                        <div class="page-wrapper">
                            <!-- [ Main Content ] start -->
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="card User-Activity">
                                        <div class="card-header">
                                            <h5>Agende sua consulta</h5>
                                            @if ($user->type === 'admin')
                                                <div class="card-header-right">
                                                    <a href="{{ route('patients.edit', $patient->id) }}" class="btn btn-icon btn-outline-primary">
                                                        <i class="feather icon-edit"></i>
                                                    </a>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="card-block pb-5" style="height: 700px;">
                                            <div class="row align-items-center justify-content-center mb-4">
                                                <div class="col-auto">
                                                    <img class="img-fluid rounded-circle" style="width:80px;" src="/img/pictures/{{ $patient->image }}" alt="doctor">
                                                </div>
                                                <div class="col">
                                                    <h5>{{ $patient->name }}</h5>
                                                    <span>{{ $patient->patient->blood_type ?? 'Geral' }}</span>
                                                </div>
                                            </div>
                                            <div id="calendar"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- [ Main Content ] end -->
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <form id='send_form' action='{{ route('appointments.store') }}' method='POST' style='display:none'>
        {{ csrf_field() }}
        <input id='send_date' type='hidden' name='date' value=''>
        <input id='send_doctor' type='hidden' name='doctor' value=''>
        <input id='send_patient' type='hidden' name='patient' value=''>
    </form>
    <script>
        $(".config-avatar").click(function(event) {
            var previewImg = $(this).children("img");

            $(this)
                .siblings()
                .children("input")
                .trigger("click");

            $(this)
                .siblings()
                .children("input")
                .change(function() {
                    var reader = new FileReader();

                    reader.onload = function(e) {
                        var urll = e.target.result;
                        $(previewImg).attr("src", urll);
                        previewImg.parent().css("background", "transparent");
                        previewImg.show();
                    };
                    reader.readAsDataURL(this.files[0]);
                });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                height: 'calc(100% - 80px)',
                expandRows: true,
                locale: 'pt-br',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                initialView: 'timeGridWeek',
                events: "{{ route('appointments.patient.load', ['id' => $patient->id]) }}",
                allDaySlot: false,
                slotMinTime: '08:00:00',
                slotMaxTime: '19:00:00',
                slotDuration: '01:00',
                showNonCurrentDates: false,
                //hiddenDays: [0, 6],
                businessHours: [
                    {
                        daysOfWeek: [ 1, 2, 3, 4, 5, 6 ],
                        startTime: '08:00',
                        endTime: '13:00'
                    },
                    {
                        daysOfWeek: [ 1, 2, 3, 4, 5, 6 ],
                        startTime: '14:00',
                        endTime: '19:00'
                    }
                ],
                dateClick: async function(info) {
                    if (info.date.getHours() === 13) return;
                    const apiUrl = `/doctors/available?date=${info.dateStr}`;
                    const apiResponse = await fetch(apiUrl).then(response => response.json()).catch(console.error);

                    if ( !isNaN(new Date(apiResponse)) ) {
                        Toast.fire({
                            icon: 'error',
                            title: 'A data tem que ser maior que a data atual.'
                        })
                    } else {
                        var html = '<div class="row">';
                        apiResponse.forEach(doctor => {
                            html += `
                                <div class="col-md-6 col-xl-6">
                                    <div class="card hover-md" onclick="setAppointment('${info.dateStr}', '${doctor.id}', '${doctor.name}', '{{ $patient->id }}', '{{ $patient->name }}')">
                                        <div class="card-block">
                                            <div class="row align-items-center justify-content-center">
                                                <div class="col-auto">
                                                    <img class="img-fluid rounded-circle" style="width:80px;" src="/img/pictures/${doctor.image}" alt="doctor">
                                                </div>
                                                <div class="col">
                                                    <h5>${doctor.name}</h5>
                                                    <span>${doctor.specialty ?? 'Geral'}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `;
                        });
                        html += '</div>';

                        Swal.fire({
                            title: `Médicos disponíveis:`,
                            html: html,
                            width: '80%',
                            showConfirmButton: false,
                            showCloseButton: true
                        });
                    }

                    // !! ANTIGO !!
                    // Swal.fire({
                    //     title: 'Tem certeza?',
                    //     text: `Agendar consulta com {{ $patient->name }}?`,
                    //     icon: 'warning',
                    //     showCancelButton: true,
                    //     customClass: {
                    //         confirmButton: 'btn btn-outline-success',
                    //         cancelButton: 'btn btn-outline-danger'
                    //     },
                    //     buttonsStyling: false,
                    //     confirmButtonText: 'Sim, pode agendar!',
                    //     cancelButtonText: 'Cancelar',
                    //     reverseButtons: true
                    // }).then((result) => {
                    //     if (result.value) {
                    //         $('#send_date').val(info.dateStr);
                    //         $('#send_form').submit();
                    //     }
                    // });
                    // onclick="getPatient('${info.dateStr}', ${doctor.id}, '${doctor.name}')">
                },
            });
            calendar.render();
        });
    </script>
    <script>
        function setAppointment(date, doctorId, doctorName, patientId, patientName) {
            Swal.close();
            Swal.fire({
                title: 'Tem certeza?',
                text: `Agendar consulta com ${doctorName} para ${patientName}?`,
                icon: 'warning',
                showCancelButton: true,
                customClass: {
                    confirmButton: 'btn btn-outline-success',
                    cancelButton: 'btn btn-outline-danger'
                },
                buttonsStyling: false,
                confirmButtonText: 'Sim, pode agendar!',
                cancelButtonText: 'Cancelar',
                reverseButtons: true
            }).then((result) => {
                if (result.value) {
                    $('#send_date').val(date);
                    $('#send_doctor').val(doctorId);
                    $('#send_patient').val(patientId);
                    $('#send_form').submit();
                }
            });
        }
    </script>
    <!-- [ Main Content ] end -->
@endsection
