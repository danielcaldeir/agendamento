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
                                        <li class="breadcrumb-item"><a href="{{ route('doctors.show', $doctor->id) }}">Médico</a></li>
                                        <li class="breadcrumb-item"><a href="javascript:">Edit</a></li>
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
                                            <h5>{{ $doctor->name }}</h5>
                                        </div>
                                        <div class="card-block pb-0">
                                            <form class="link-form" action='{{ route('doctors.update', $doctor->id) }}' method='POST' enctype="multipart/form-data">
                                                {{ csrf_field() }}
                                                <div class="bg-c-blue config-avatar shadow-3">
                                                    <img src='{{ asset('img/pictures/' . $doctor->image) }}'>
                                                </div>
                                                <div class="controls" style="display: none;">
                                                    <input type="file" name="image"/>
                                                </div>
                                                <input name="_method" type="hidden" value="PUT">
                                                <input id='doctor-name' class='form-control' type='text' name='name' placeholder="Nome" value='{{ $doctor->name }}'>
                                                <input class='form-control mt-3' type='text' name='specialty' placeholder="Especialização" value='{{ $doctor->doctor->specialty ?? '' }}'>
                                                <br>
                                                <button class='btn btn-outline-primary' type='submit'>Editar</button>
                                            </form>
                                            <form action="{{ route('doctors.destroy', $doctor->id) }}" method="post" class="mb-3">
                                                {{ csrf_field() }}
                                                <input name="_method" type="hidden" value="DELETE">
                                                <button class='btn btn-block btn-outline-danger' type='submit'>Remover</button>
                                            </form>
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

                    Swal.fire({
                        title: 'Tem certeza?',
                        text: `Agendar consulta com {{ $doctor->name }}?`,
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
                            $('#send_date').val(info.dateStr);
                            $('#send_form').submit();
                        }
                    });
                },
            });
            calendar.render();
        });
    </script>
    <!-- [ Main Content ] end -->
@endsection
