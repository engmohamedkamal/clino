<div class="mb-3">
  <label>Availability Schedule</label>
  <input class="form-control" name="availability_schedule" value="{{ old('availability_schedule', optional($info)->availability_schedule) }}">
</div>

<div class="mb-3">
  <label>Gender</label>
  <input class="form-control" name="gender" value="{{ old('gender', optional($info)->gender) }}">
</div>

<div class="mb-3">
  <label>Date of Birth</label>
  <input type="date" class="form-control" name="dob" value="{{ old('dob', optional($info)->dob) }}">
</div>

<div class="mb-3">
  <label>Specialization</label>
  <input class="form-control" name="specialization" value="{{ old('specialization', optional($info)->specialization) }}">
</div>

<div class="mb-3">
  <label>License Number</label>
  <input class="form-control" name="license_number" value="{{ old('license_number', optional($info)->license_number) }}">
</div>

<div class="mb-3">
  <label>Address</label>
  <input class="form-control" name="address" value="{{ old('address', optional($info)->address) }}">
</div>

<div class="mb-3">
  <label>Image</label>
  <input type="file" class="form-control" name="image">
  @if(optional($info)->image)
    <div class="mt-2">
      <img src="{{ asset('storage/'.$info->image) }}" style="max-width:120px;border-radius:8px;">
    </div>
  @endif
</div>

<div class="mb-3">
  <label>About</label>
  <textarea class="form-control" name="about" rows="4">{{ old('about', optional($info)->about) }}</textarea>
</div>
