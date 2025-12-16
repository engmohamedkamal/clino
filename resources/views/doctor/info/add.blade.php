{{-- resources/views/doctor/info/add.blade.php --}}
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- Bootstrap CSS --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <title>Doctor Info</title>
</head>

<body class="bg-light">

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-9 col-lg-8">

            {{-- Success message --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form class="card shadow-sm p-4"
                  method="POST"
                  action="{{ route('create-doctor', Auth::id()) }}"
                  enctype="multipart/form-data">
                @csrf

                <h3 class="text-center mb-4">Add / Edit Your Info</h3>

                <div class="row">
                    {{-- Gender --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Gender</label>
                        <select name="gender" class="form-select @error('gender') is-invalid @enderror">
                            <option disabled {{ old('gender', $doctor->gender ?? '') == '' ? 'selected' : '' }}>
                                Select gender
                            </option>
                            <option value="Male" {{ old('gender', $doctor->gender ?? '') == 'Male' ? 'selected' : '' }}>
                                Male
                            </option>
                            <option value="Female" {{ old('gender', $doctor->gender ?? '') == 'Female' ? 'selected' : '' }}>
                                Female
                            </option>
                        </select>
                        @error('gender')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- DOB --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Date of Birth</label>
                        <input type="date" name="dob"
                               class="form-control @error('dob') is-invalid @enderror"
                               value="{{ old('dob', $doctor->dob ?? '') }}">
                        @error('dob')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Specialization --}}
                <div class="mb-3">
                    <label class="form-label">Specialization</label>
                    <input type="text" name="Specialization"
                           class="form-control @error('Specialization') is-invalid @enderror"
                           value="{{ old('Specialization', $doctor->Specialization ?? '') }}"
                           placeholder="Enter specialization">
                    @error('Specialization')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Availability schedule --}}
                <div class="mb-3">
                    <label class="form-label">Availability schedule</label>
                    <input type="text" name="availability_schedule"
                           class="form-control @error('availability_schedule') is-invalid @enderror"
                           value="{{ old('availability_schedule', $doctor->availability_schedule ?? '') }}"
                           placeholder="Enter availability schedule">
                    @error('availability_schedule')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- License Number --}}
                <div class="mb-3">
                    <label class="form-label">License Number</label>
                    <input type="text" name="license_number"
                           class="form-control @error('license_number') is-invalid @enderror"
                           value="{{ old('license_number', $doctor->license_number ?? '') }}"
                           placeholder="Enter license number">
                    @error('license_number')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Address --}}
                <div class="mb-3">
                    <label class="form-label">Address</label>
                    <input type="text" name="address"
                           class="form-control @error('address') is-invalid @enderror"
                           value="{{ old('address', $doctor->address ?? '') }}"
                           placeholder="Enter address">
                    @error('address')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- About --}}
                <div class="mb-3">
                    <label class="form-label">About Doctor</label>
                    <textarea name="about" rows="4"
                              class="form-control @error('about') is-invalid @enderror"
                              placeholder="Write about the doctor">{{ old('about', $doctor->about ?? '') }}</textarea>
                    @error('about')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Image --}}
                <div class="mb-3">
                    <label class="form-label">Doctor Image</label>

                    @if(isset($doctor) && $doctor->image)
                        <div class="mb-2">
                            <img src="{{ asset($doctor->image) }}" class="img-thumbnail" style="width: 140px; height:auto;">
                        </div>
                    @endif

                    <input type="file" name="image" class="form-control @error('image') is-invalid @enderror">
                    @error('image')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror

                    <div class="form-text">
                        Leave empty to keep current image.
                    </div>
                </div>

                <button type="submit" class="btn btn-primary w-100">
                    Save
                </button>

            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
        crossorigin="anonymous"></script>
</body>
</html>
