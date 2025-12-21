@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">

    <div class="row">
        <div class="col-12 col-lg-10 mx-auto">

            {{-- Page Title --}}
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="mb-0">Edit Doctor Info</h4>
                <a href="{{ route('dashboard') }}" class="btn btn-outline-primary btn-sm">Exit</a>
            </div>

            {{-- Success / Error Alerts --}}
            @if(session('success'))
                <div id="successAlert" class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div id="errorAlert" class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            {{-- Validation Errors --}}
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- FORM CARD --}}
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4 p-md-5">

                    <form method="POST" action="{{ route('doctor-info.update') }}" enctype="multipart/form-data">
                        @csrf

                        <div class="row g-4">

                            {{-- Gender --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Gender</label>
                                <select name="gender" class="form-select @error('gender') is-invalid @enderror">
                                    @foreach(['Male','Female','Other'] as $gender)
                                        <option value="{{ $gender }}"
                                            {{ (old('gender', $doctorInfo->gender) == $gender) ? 'selected' : '' }}>
                                            {{ $gender }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('gender') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            {{-- Specialization --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Specialization</label>
                                <input type="text" name="specialization"
                                    class="form-control @error('specialization') is-invalid @enderror"
                                    value="{{ old('specialization', $doctorInfo->specialization) }}">
                                @error('specialization') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            {{-- License Number --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">License Number</label>
                                <input type="text" name="license_number"
                                    class="form-control @error('license_number') is-invalid @enderror"
                                    value="{{ old('license_number', $doctorInfo->license_number) }}">
                                @error('license_number') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            {{-- Profile Picture --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Profile Picture</label>
                                <input type="file" name="image"
                                    class="form-control @error('image') is-invalid @enderror">
                                @error('image') <small class="text-danger">{{ $message }}</small> @enderror

                                @if($doctorInfo->image)
                                    <div class="mt-2">
                                        <img src="{{ asset('storage/' . $doctorInfo->image) }}"
                                            style="width: 80px; height: 80px; object-fit: cover; border-radius: 12px;">
                                    </div>
                                @endif
                            </div>

                            {{-- DOB --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Date of Birth</label>
                                <input type="date" name="dob"
                                    class="form-control @error('dob') is-invalid @enderror"
                                    value="{{ old('dob', $doctorInfo->dob) }}">
                                @error('dob') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            {{-- Address --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Address</label>
                                <input type="text" name="address"
                                    class="form-control @error('address') is-invalid @enderror"
                                    value="{{ old('address', $doctorInfo->address) }}">
                                @error('address') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            {{-- Availability Schedule --}}
                            <div class="col-12">
                                <label class="form-label fw-semibold">Availability Schedule</label>
                                <input type="text" name="availability_schedule"
                                    class="form-control @error('availability_schedule') is-invalid @enderror"
                                    value="{{ old('availability_schedule', $doctorInfo->availability_schedule) }}">
                                @error('availability_schedule') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            {{-- Social URLs --}}
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Facebook URL</label>
                                <input type="url" name="facebook_url"
                                    class="form-control @error('facebook_url') is-invalid @enderror"
                                    value="{{ old('facebook_url', $doctorInfo->facebook_url) }}">
                                @error('facebook_url') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Instagram URL</label>
                                <input type="url" name="instagram_url"
                                    class="form-control @error('instagram_url') is-invalid @enderror"
                                    value="{{ old('instagram_url', $doctorInfo->instagram_url) }}">
                                @error('instagram_url') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Twitter URL</label>
                                <input type="url" name="twitter_url"
                                    class="form-control @error('twitter_url') is-invalid @enderror"
                                    value="{{ old('twitter_url', $doctorInfo->twitter_url) }}">
                                @error('twitter_url') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            {{-- About --}}
                            <div class="col-12">
                                <label class="form-label fw-semibold">About</label>
                                <textarea name="about" rows="5"
                                    class="form-control @error('about') is-invalid @enderror">{{ old('about', $doctorInfo->about) }}</textarea>
                                @error('about') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            {{-- BUTTON --}}
                            <div class="col-12 mt-3">
                                <button type="submit" class="btn btn-primary w-100 py-2">
                                    Update
                                </button>
                            </div>

                        </div> {{-- end row --}}
                    </form>

                </div>
            </div>
        </div>
    </div>

</div>
@endsection

{{-- auto hide alerts --}}
@push('scripts')
<script>
    setTimeout(() => {
        ['successAlert','errorAlert'].forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                el.style.transition = '0.5s';
                el.style.opacity = '0';
                setTimeout(() => el.remove(), 500);
            }
        })
    }, 3000);
</script>
@endpush
