@extends('layouts.app')

@section('title', '| Médico')
@section('sidebar_doctors', 'active')

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
                                        <h5 class="m-b-10">Médico</h5>
                                    </div>
                                    <ul class="breadcrumb">
                                        <li class="breadcrumb-item"><a href="{{ route('dashboard') }}"><i class="feather icon-home"></i></a></li>
                                        <li class="breadcrumb-item"><a href="{{ route('doctors.index') }}">Médicos</a></li>
                                        <li class="breadcrumb-item"><a href="javascript:">Médico</a></li>
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
                                                    <a href="{{ route('doctors.edit', $doctor->id) }}" class="btn btn-icon btn-outline-primary">
                                                        <i class="feather icon-edit"></i>
                                                    </a>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="card-block pb-5" style="height: 700px;">
                                            <div class="row align-items-center justify-content-center mb-4">
                                                <div class="col-auto">
                                                    <img class="img-fluid rounded-circle" style="width:80px;" src="/img/pictures/{{ $doctor->image }}" alt="doctor">
                                                </div>
                                                <div class="col">
                                                    <h5>{{ $doctor->name }}</h5>
                                                    <span>{{ $doctor->doctor->specialty ?? 'Geral' }}</span>
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
        $(document).ready(function() {
            $('#tb-patients').DataTable();
        } );
    </script>
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
                events: "{{ route('appointments.doctor.load', ['id' => $doctor->id]) }}",
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
                    const apiUrl = `/patients/available?date=${info.dateStr}`;
                    const apiResponse = await fetch(apiUrl).then(response => response.json());

                    var html = `
                        <table id="tb-patients" class="display" style="width:100%">
                            <thead>
                                <tr>
                                    <th>Imagem</th>
                                    <th>Nome</th>
                                    <th>Editar</th>
                                </tr>
                            </thead>
                            <tbody>
                    `;
                    apiResponse.forEach(patient => {
                        html += `
                            <tr>
                                <td style="width: 60px;">
                                    <img
                                        class="rounded-circle"
                                        style="width:40px;"
                                        src="/img/pictures/${patient.image}"
                                        alt="patient-image"
                                    >
                                </td>
                                <td><h5>${patient.name}</h5></td>
                                <td>
                                    <button class="btn btn-icon btn-outline-primary" onclick="setAppointment('${info.dateStr}', '{{ $doctor->id }}', '{{ $doctor->name }}', ${patient.id}, '${patient.name}')">
                                        <i class="feather icon-play"></i>
                                    </button>
                                </td>
                            </tr>
                        `;
                    });
                    html += `
                            </tbody>
                        </table>
                    `;

                    Swal.fire({
                        title: `Pacientes disponíveis:`,
                        html: html,
                        width: '80%',
                        showConfirmButton: false,
                        showCloseButton: true
                    });

                    // $('#tb-patients').DataTable();

                    // !!! ANTIGO !!!!
                    // Swal.fire({
                    //     title: 'Tem certeza?',
                    //     text: `Agendar consulta com {{ $doctor->name }}?`,
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
