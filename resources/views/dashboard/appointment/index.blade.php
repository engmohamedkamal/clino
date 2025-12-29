@extends('layouts.dash')

@section('dash-content')
    <main class="main">
        <header class="topbar">
            <div class="d-flex align-items-center gap-2">
                <button class="btn icon-btn d-lg-none" type="button" data-bs-toggle="offcanvas"
                    data-bs-target="#mobileSidebar" aria-controls="mobileSidebar">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <div class="page-title">Dashboard</div>
            </div>
        </header>

        <!-- Content -->
        <section class="content-area">
            <div class="h-100 d-flex align-items-start justify-content-center pt-3 pt-md-4">
                <div class="appointment-card">
                    <h3 class="appointment-title mb-4">Book Appointment</h3>

                    {{-- SUCCESS --}}
                    @if (session('success'))
                        <div id="successAlert" class="alert alert-success mb-3">
                            {{ session('success') }}
                        </div>
                    @endif
                    <form method="POST" action="{{ route('appointment.store') }}">
                        @csrf

                        <div class="row g-3">
                            <!-- LEFT COLUMN -->
                            @if (Auth()->user()->role != 'patient')


                                <div class="col-md-6">
                                    <!-- Patient Name -->
                                    <div class="mb-3">
                                        <label class="form-label appointment-label" for="patient_name">Patient Name</label>
                                        <input type="text" id="patient_name" name="patient_name"
                                            value="{{ old('patient_name') }}"
                                            class="form-control appointment-control @error('patient_name') is-invalid @enderror"
                                            placeholder="Enter Name">
                                        @error('patient_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Patient Number -->
                                    <div class="mb-3">
                                        <label class="form-label appointment-label" for="patient_number">Patient Number</label>
                                        <input type="tel" id="patient_number" name="patient_number"
                                            value="{{ old('patient_number') }}"
                                            class="form-control appointment-control @error('patient_number') is-invalid @enderror"
                                            placeholder="Enter Phone Number">
                                        @error('patient_number')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Date Of Birth -->
                                    <div class="mb-3">
                                        <label class="form-label appointment-label" for="dob">Date of birth</label>
                                        <input type="date" id="dob" name="dob" value="{{ old('dob') }}"
                                            class="form-control appointment-control @error('dob') is-invalid @enderror">
                                        @error('dob')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>

                                    <!-- Gender -->
                                    <div class="mb-3">
                                        <label class="form-label appointment-label" for="gender">Gender</label>
                                        <select id="gender" name="gender"
                                            class="form-select appointment-control @error('gender') is-invalid @enderror">
                                            <option value="" disabled {{ old('gender') ? '' : 'selected' }}>Select</option>
                                            <option value="male" {{ old('gender') === 'male' ? 'selected' : '' }}>Male</option>
                                            <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Female
                                            </option>
                                        </select>
                                        @error('gender')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                </div>
                            @endif
                            <!-- RIGHT COLUMN -->
                            <div class="{{ Auth()->user()->role != 'patient' ? 'col-md-6' : 'col-md-12' }}">

                                <!-- Appointment Date -->
                                <div class="mb-3">
                                    <label class="form-label appointment-label" for="appointment_date">Select date</label>
                                    <input type="date" id="appointment_date" name="appointment_date"
                                        value="{{ old('appointment_date') }}"
                                        class="form-control appointment-control @error('appointment_date') is-invalid @enderror">
                                    @error('appointment_date')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Appointment Time -->
                                <div class="mb-3">
                                    <label class="form-label appointment-label" for="appointment_time">Select Time</label>
                                    <input type="time" id="appointment_time" name="appointment_time"
                                        value="{{ old('appointment_time') }}"
                                        class="form-control appointment-control @error('appointment_time') is-invalid @enderror">
                                    @error('appointment_time')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Doctor Name -->
                                <div class="mb-3">
                                    <label class="form-label appointment-label" for="doctor_name">Doctor Name</label>
                                    <select id="doctor_name" name="doctor_name"
                                        class="form-select appointment-control @error('doctor_name') is-invalid @enderror">
                                        <option value="" disabled {{ old('doctor_name') ? '' : 'selected' }}>
                                            Select Doctor
                                        </option>

                                        @foreach ($doctors as $doctor)
                                            <option value="{{ $doctor->name }}" {{ old('doctor_name') === $doctor->name ? 'selected' : '' }}>
                                                {{ $doctor->name }}
                                            </option>
                                        @endforeach
                                    </select>

                                    @error('doctor_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Reason -->
                                <div class="mb-3">
                                    <label class="form-label appointment-label" for="reason">Reason</label>
                                    <textarea id="reason" name="reason" rows="4"
                                        class="form-control appointment-control appointment-textarea @error('reason') is-invalid @enderror"
                                        placeholder="Describe your reason">{{ old('reason') }}</textarea>
                                    @error('reason')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Buttons -->
                            <div class="d-flex justify-content-end gap-2 gap-md-3 mt-3 mt-md-4">
                                <a href="{{ url()->previous() }}" class="btn btn-light btn-cancel">Cancel</a>
                                <button type="submit" class="btn btn-primary btn-book">Book Appointment</button>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </section>
    </main>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const el = document.getElementById('successAlert');
            if (!el) return;
            setTimeout(() => {
                el.classList.add('fade');
                el.classList.remove('show');
                el.style.display = 'none';
            }, 3000);
        });
    </script>
@endsection