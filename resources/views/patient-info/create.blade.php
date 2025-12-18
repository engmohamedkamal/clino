<div class="container py-4">
  <div class="row justify-content-center">
    <div class="col-md-8">
      <div class="card shadow-sm">
        <div class="card-header">
          <h4 class="mb-0">Add Patient Info</h4>
        </div>
        <div class="card-body">

          @if(session('info'))
            <div class="alert alert-info">{{ session('info') }}</div>
          @endif

          <form action="{{ route('patient-info.store') }}" method="POST">
            @csrf

            {{-- Gender --}}
            <div class="mb-3">
              <label class="form-label">Gender</label>
              <select name="gender" class="form-select">
                <option value="" selected disabled>Select gender</option>
                <option value="male" {{ old('gender')=='male' ? 'selected' : '' }}>Male</option>
                <option value="female" {{ old('gender')=='female' ? 'selected' : '' }}>Female</option>
              </select>
              @error('gender') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            {{-- DOB --}}
            <div class="mb-3">
              <label class="form-label">Date of Birth</label>
              <input type="date" name="dob" class="form-control" value="{{ old('dob') }}">
              @error('dob') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            {{-- Phone --}}
            <div class="mb-3">
              <label class="form-label">Phone</label>
              <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
              @error('phone') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            {{-- Address --}}
            <div class="mb-3">
              <label class="form-label">Address</label>
              <input type="text" name="address" class="form-control" value="{{ old('address') }}">
              @error('address') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            {{-- Blood Type --}}
            <div class="mb-3">
              <label class="form-label">Blood Type</label>
              <input type="text" name="blood_type" class="form-control" value="{{ old('blood_type') }}">
              @error('blood_type') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="row">
              <div class="col-md-6 mb-3">
                <label class="form-label">Weight (kg)</label>
                <input type="number" step="0.1" name="weight" class="form-control" value="{{ old('weight') }}">
                @error('weight') <small class="text-danger">{{ $message }}</small> @enderror
              </div>
              <div class="col-md-6 mb-3">
                <label class="form-label">Height (cm)</label>
                <input type="number" step="0.1" name="height" class="form-control" value="{{ old('height') }}">
                @error('height') <small class="text-danger">{{ $message }}</small> @enderror
              </div>
            </div>

            {{-- Emergency Contact Name --}}
            <div class="mb-3">
              <label class="form-label">Emergency Contact Name</label>
              <input type="text" name="emergency_contact_name" class="form-control"
                     value="{{ old('emergency_contact_name') }}">
              @error('emergency_contact_name') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            {{-- Emergency Contact Phone --}}
            <div class="mb-3">
              <label class="form-label">Emergency Contact Phone</label>
              <input type="text" name="emergency_contact_phone" class="form-control"
                     value="{{ old('emergency_contact_phone') }}">
              @error('emergency_contact_phone') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            {{-- Medical History --}}
            <div class="mb-3">
              <label class="form-label">Medical History</label>
              <textarea name="medical_history" rows="3" class="form-control">{{ old('medical_history') }}</textarea>
              @error('medical_history') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            {{-- Allergies --}}
            <div class="mb-3">
              <label class="form-label">Allergies</label>
              <textarea name="allergies" rows="2" class="form-control">{{ old('allergies') }}</textarea>
              @error('allergies') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            {{-- Current Medications --}}
            <div class="mb-3">
              <label class="form-label">Current Medications</label>
              <textarea name="current_medications" rows="2" class="form-control">{{ old('current_medications') }}</textarea>
              @error('current_medications') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            {{-- Notes --}}
            <div class="mb-3">
              <label class="form-label">Notes</label>
              <textarea name="notes" rows="2" class="form-control">{{ old('notes') }}</textarea>
              @error('notes') <small class="text-danger">{{ $message }}</small> @enderror
            </div>

            <div class="d-flex justify-content-between">
              <a href="{{ route('patient-info.index') }}" class="btn btn-secondary">Back</a>
              <button type="submit" class="btn btn-primary">Save</button>
            </div>

          </form>
        </div>
      </div>
    </div>
  </div>
</div>

