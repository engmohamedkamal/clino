<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <title>New Appointment</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap CSS (v5) -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

  <style>
    body {
      background-color: #f5f5f5;
    }

    .appointment-card {
      max-width: 600px;
      margin: 40px auto;
    }
  </style>
</head>

<body>

  <div class="container">
    <div class="card shadow-sm appointment-card">
      <div class="card-body">
        <h4 class="mb-4 text-center">Create Appointment</h4>

        {{-- رسالة النجاح --}}
        @if(session('success'))
          <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          </div>
        @endif

        <form method="POST" action="{{ route('appointment.store') }}" novalidate>
          @csrf

          <!-- Patient Name -->
          <div class="mb-3">
            <label for="patient_name" class="form-label">Patient Name</label>
            <input type="text" class="form-control" id="patient_name" name="patient_name"
                   value="{{ old('patient_name') }}" required>
            @error('patient_name')
              <small class="text-danger">{{ $message }}</small>
            @enderror
          </div>

          <!-- Doctor Name -->
          <div class="mb-3">
            <label for="doctor_name" class="form-label">Doctor Name</label>
            <select name="doctor_name" id="doctor_name" class="form-select" required>
              <option value="" disabled {{ old('doctor_name') ? '' : 'selected' }}>Select a doctor</option>
              @foreach($doctors as $doctor)
                <option value="{{ $doctor->name }}" {{ old('doctor_name') == $doctor->name ? 'selected' : '' }}>
                  {{ $doctor->name }}
                </option>
              @endforeach
            </select>
            @error('doctor_name')
              <small class="text-danger">{{ $message }}</small>
            @enderror
          </div>

          <!-- Gender -->
          <div class="mb-3">
            <label for="gender" class="form-label">Gender</label>
            <select class="form-select" id="gender" name="gender" required>
              <option value="" disabled {{ old('gender') ? '' : 'selected' }}>Select gender</option>
              <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
              <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
            </select>
            @error('gender')
              <small class="text-danger">{{ $message }}</small>
            @enderror
          </div>

          <!-- Appointment Date & Time -->
          <div class="row">
            <div class="col-md-6 mb-3">
              <label for="appointment_date" class="form-label">Appointment Date</label>
              <input type="date" class="form-control" id="appointment_date" name="appointment_date"
                     value="{{ old('appointment_date') }}" required>
              @error('appointment_date')
                <small class="text-danger">{{ $message }}</small>
              @enderror
            </div>

            <div class="col-md-6 mb-3">
              <label for="appointment_time" class="form-label">Appointment Time</label>
              <input type="time" class="form-control" id="appointment_time" name="appointment_time"
                     value="{{ old('appointment_time') }}" required>
              @error('appointment_time')
                <small class="text-danger">{{ $message }}</small>
              @enderror
            </div>
          </div>

          <!-- Patient Number -->
          <div class="mb-3">
            <label for="patient_number" class="form-label">Patient Number</label>
            <input type="text" class="form-control" id="patient_number" name="patient_number"
                   value="{{ old('patient_number') }}" required>
            @error('patient_number')
              <small class="text-danger">{{ $message }}</small>
            @enderror
          </div>

          <!-- Date of Birth -->
          <div class="mb-3">
            <label for="dob" class="form-label">Date of Birth</label>
            <input type="date" class="form-control" id="dob" name="dob"
                   value="{{ old('dob') }}" required>
            @error('dob')
              <small class="text-danger">{{ $message }}</small>
            @enderror
          </div>

          <!-- Reason -->
          <div class="mb-3">
            <label for="reason" class="form-label">Reason (optional)</label>
            <textarea class="form-control" id="reason" name="reason" rows="3"
                      placeholder="Reason for appointment...">{{ old('reason') }}</textarea>
            @error('reason')
              <small class="text-danger">{{ $message }}</small>
            @enderror
          </div>

          <!-- Submit -->
          <div class="d-grid">
            <button type="submit" class="btn btn-primary">Save Appointment</button>
          </div>

        </form>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS (optional) -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
