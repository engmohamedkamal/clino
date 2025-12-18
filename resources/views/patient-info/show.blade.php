
<div class="container py-4">
  <div class="row justify-content-center">
    <div class="col-md-8">

      @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
      @endif

      <div class="card shadow-sm">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h4 class="mb-0">
            Patient Info - {{ $patientInfo->user->name ?? 'Unknown User' }}
          </h4>
          <div>
            <a href="{{ route('patient-info.edit', $patientInfo->id) }}" class="btn btn-sm btn-warning">Edit</a>
            <a href="{{ route('patient-info.index') }}" class="btn btn-sm btn-secondary">Back</a>
          </div>
        </div>
        <div class="card-body">

          <h6 class="text-muted mb-3">Basic Info</h6>
          <p><strong>Gender:</strong> {{ $patientInfo->gender ?? '-' }}</p>
          <p><strong>Date of Birth:</strong> {{ $patientInfo->dob }}</p>
          <p><strong>Phone:</strong> {{ $patientInfo->phone ?? 0 }}</p>
          <p><strong>Address:</strong> {{ $patientInfo->address ?? '-' }}</p>
          <p><strong>Blood Type:</strong> {{ $patientInfo->blood_type ?? '-' }}</p>
          <p><strong>Weight:</strong> {{ $patientInfo->weight ? $patientInfo->weight . ' kg' : '-' }}</p>
          <p><strong>Height:</strong> {{ $patientInfo->height ? $patientInfo->height . ' cm' : '-' }}</p>

          <hr>

          <h6 class="text-muted mb-3">Emergency Contact</h6>
          <p><strong>Name:</strong> {{ $patientInfo->emergency_contact_name ?? '-' }}</p>
          <p><strong>Phone:</strong> {{ $patientInfo->emergency_contact_phone ?? '-' }}</p>

          <hr>

          <h6 class="text-muted mb-3">Medical Details</h6>
          <p><strong>Medical History:</strong><br>
            {{ $patientInfo->medical_history ?? 'No data' }}
          </p>
          <p><strong>Allergies:</strong><br>
            {{ $patientInfo->allergies ?? 'No data' }}
          </p>
          <p><strong>Current Medications:</strong><br>
            {{ $patientInfo->current_medications ?? 'No data' }}
          </p>
          <p><strong>Notes:</strong><br>
            {{ $patientInfo->notes ?? 'No data' }}
          </p>

          <hr>
          <p class="text-muted mb-0">
            Created at: {{ $patientInfo->created_at->format('Y-m-d H:i') }} |
            Last updated: {{ $patientInfo->updated_at->diffForHumans() }}
          </p>

        </div>
      </div>

    </div>
  </div>
</div>
