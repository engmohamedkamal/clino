@extends('layouts.app') {{-- غيّرها لو عندك Layout تاني --}}

@section('content')
<div class="container-fluid py-4">

    <div class="row">
        {{-- مساحة فاضية لو عندك سايدبار في الـ layout --}}
        <div class="col-12 col-lg-10 mx-auto">

            {{-- عنوان الصفحة + تنبيهات --}}
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="mb-0">Doctor info</h4>

                {{-- مثال لزر خروج/رجوع --}}
                <a href="{{ route('dashboard') }}" class="btn btn-outline-primary btn-sm">Exit</a>
            </div>

            {{-- Alerts --}}
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

            {{-- Errors عامة --}}
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- كارت الفورم --}}
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-body p-4 p-md-5">

                    <form method="POST"
                          action="{{ isset($doctorInfo) ? route('doctor-info.update') : route('doctor-info.store') }}"
                          enctype="multipart/form-data">
                        @csrf

                        <div class="row g-4">

                            {{-- Gender --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Gender</label>
                                <select name="gender"
                                        class="form-select @error('gender') is-invalid @enderror">
                                    <option value="" disabled
                                        {{ old('gender', $doctorInfo->gender ?? null) ? '' : 'selected' }}>
                                        Select
                                    </option>
                                    @foreach (['Male','Female','Other'] as $gender)
                                        <option value="{{ $gender }}"
                                            {{ old('gender', $doctorInfo->gender ?? null) == $gender ? 'selected' : '' }}>
                                            {{ $gender }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('gender')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            {{-- Specialization --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Specialization</label>
                                <input type="text"
                                       name="specialization"
                                       class="form-control @error('specialization') is-invalid @enderror"
                                       placeholder="Enter Specialization"
                                       value="{{ old('specialization', $doctorInfo->specialization ?? '') }}">
                                @error('specialization')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            {{-- License Number --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">License Number</label>
                                <input type="text"
                                       name="license_number"
                                       class="form-control @error('license_number') is-invalid @enderror"
                                       placeholder="Enter Number"
                                       value="{{ old('license_number', $doctorInfo->license_number ?? '') }}">
                                @error('license_number')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            {{-- Profile Picture --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Profile Picture</label>
                                <input type="file"
                                       name="image"
                                       class="form-control @error('image') is-invalid @enderror">
                                @error('image')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror

                                @if(isset($doctorInfo) && $doctorInfo->image)
                                    <div class="mt-2">
                                        <img src="{{ asset('storage/'.$doctorInfo->image) }}"
                                             alt="Profile"
                                             style="width: 80px; height: 80px; object-fit: cover; border-radius: 12px;">
                                    </div>
                                @endif
                            </div>

                            {{-- Date of Birth --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Date of Birth</label>
                                <input type="date"
                                       name="dob"
                                       class="form-control @error('dob') is-invalid @enderror"
                                       value="{{ old('dob', isset($doctorInfo->dob) ? $doctorInfo->dob : '') }}">
                                @error('dob')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            {{-- Address --}}
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Address</label>
                                <input type="text"
                                       name="address"
                                       class="form-control @error('address') is-invalid @enderror"
                                       placeholder="Enter Address"
                                       value="{{ old('address', $doctorInfo->address ?? '') }}">
                                @error('address')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            {{-- Availability schedule --}}
                            <div class="col-12">
                                <label class="form-label fw-semibold">Availability schedule</label>
                                <input type="text"
                                       name="availability_schedule"
                                       class="form-control @error('availability_schedule') is-invalid @enderror"
                                       placeholder="Enter Availability schedule"
                                       value="{{ old('availability_schedule', $doctorInfo->availability_schedule ?? '') }}">
                                @error('availability_schedule')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            {{-- Social URLs --}}
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Facebook URL</label>
                                <input type="url"
                                       name="facebook_url"
                                       class="form-control @error('facebook_url') is-invalid @enderror"
                                       placeholder="Facebook"
    
                                       value="{{ old('facebook_url', $doctorInfo->facebook_url ?? '') }}">
                                @error('facebook_url')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Instagram URL</label>
                                <input type="url"
                                       name="instagram_url"
                                       class="form-control @error('instagram_url') is-invalid @enderror"
                                       placeholder="Instagram"
                                       value="{{ old('instagram_url', $doctorInfo->instagram_url ?? '') }}">
                                @error('instagram_url')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Twitter URL</label>
                                <input type="url"
                                       name="twitter_url"
                                       class="form-control @error('twitter_url') is-invalid @enderror"
                                       placeholder="Twitter"
                                       value="{{ old('twitter_url', $doctorInfo->twitter_url ?? '') }}">
                                @error('twitter_url')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            {{-- About --}}
                            <div class="col-12">
                                <label class="form-label fw-semibold">About</label>
                                <textarea name="about"
                                          rows="5"
                                          class="form-control @error('about') is-invalid @enderror"
                                          placeholder="Write about yourself...">{{ old('about', $doctorInfo->about ?? '') }}</textarea>
                                @error('about')
                                    <small class="text-danger">{{ $message }}</small>
                                @enderror
                            </div>

                            {{-- Save button --}}
                            <div class="col-12 mt-3">
                                <button type="submit" class="btn btn-primary w-100 py-2">
                                    Save
                                </button>
                            </div>

                        </div> {{-- row --}}
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>
@endsection

{{-- إخفاء رسائل النجاح/الخطأ بعد 3 ثواني --}}
@push('scripts')
<script>
    setTimeout(() => {
        ['successAlert', 'errorAlert'].forEach(id => {
            const el = document.getElementById(id);
            if (el) {
                el.style.transition = '0.5s';
                el.style.opacity = '0';
                setTimeout(() => el.remove(), 500);
            }
        });
    }, 3000);
</script>
@endpush
