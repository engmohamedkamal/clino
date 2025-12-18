
<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Patients Info</h3>
    <a href="{{ route('patient-info.create') }}" class="btn btn-primary">Add Patient Info</a>
  </div>

  @if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
  @endif

  @if(session('info'))
    <div class="alert alert-info">{{ session('info') }}</div>
  @endif

  @if($patients->count())
    <div class="table-responsive">
      <table class="table table-striped align-middle">
        <thead>
          <tr>
            <th>#</th>
            <th>User</th>
            <th>Gender</th>
            <th>Phone</th>
            <th>Blood Type</th>
            <th>Created</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          @foreach($patients as $patient)
            <tr>
              <td>{{ $patient->id }}</td>
              <td>{{ $patient->user->name ?? 'Unknown' }}</td>
              <td>{{ $patient->gender ?? '-' }}</td>
              <td>{{ $patient->phone ?? '-' }}</td>
              <td>{{ $patient->blood_type ?? '-' }}</td>
              <td>{{ $patient->created_at->format('Y-m-d') }}</td>
              <td>
                <a href="{{ route('patient-info.show', $patient->id) }}" class="btn btn-sm btn-info">View</a>
                <a href="{{ route('patient-info.edit', $patient->id) }}" class="btn btn-sm btn-warning">Edit</a>
                <form action="{{ route('patient-info.destroy', $patient->id) }}" method="POST" class="d-inline"
                      onsubmit="return confirm('Are you sure you want to delete this record?');">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                </form>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    {{ $patients->links() }}
  @else
    <p class="text-muted">No patient info found.</p>
  @endif
</div>

