
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Appointments</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  </head>

  <body>
    <div class="container py-4">

      <h3 class="mb-4">Appointments List</h3>

      <!-- Success Alert -->
      @if(session('success'))
      <div class="alert alert-success alert-dismissible fade show" role="alert">
          {{ session('success') }}
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
      @endif

      <table class="table table-bordered table-striped align-middle">
        <thead class="table-primary">
          <tr>
            <th>#</th>
            <th>Patient Name</th>
            <th>Doctor Name</th>
            <th>Gender</th>
            <th>Date</th>
            <th>Time</th>
            <th>Reason</th>
            <th>Actions</th>
          </tr>
        </thead>

        <tbody>
          @foreach($appointments as $appointment)
          <tr>
            <td>{{ $loop->iteration }}</td>
            <td>{{ $appointment->patient_name }}</td>
            <td>{{ $appointment->doctor_name }}</td>
            <td>{{ ucfirst($appointment->gender) }}</td>
            <td>{{ $appointment->appointment_date }}</td>
            <td>{{ $appointment->appointment_time }}</td>
            <td>{{ $appointment->reason ?? '—' }}</td>
<td>
    <a href="{{ route('appointment.edit', $appointment->id) }}" 
       class="btn btn-sm btn-warning">
       Edit
    </a>

    <form action="{{ route('appointment.destroy', $appointment->id) }}" 
          method="POST" 
          style="display:inline-block;">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-sm btn-danger"
                onclick="return confirm('Are you sure you want to delete this appointment?')">
            Delete
        </button>
    </form>
</td>

          </tr>
          @endforeach
        </tbody>
      </table>

      @if($appointments->count() == 0)
        <p class="text-center text-muted">No appointments found.</p>
      @endif

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>
