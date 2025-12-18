<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Create User</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
      body {
        background-color: #f5f5f5;
      }
      .user-card {
        max-width: 600px;
        margin: 40px auto;
      }
    </style>
  </head>
  <body>

    <div class="container">

      {{-- Success Message --}}
      @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      @endif

      <div class="card shadow-sm user-card">
        <div class="card-body">
          <h4 class="mb-4 text-center">Create User</h4>

          <form action="{{ route('users.store') }}" method="POST" novalidate>
            @csrf

            {{-- Name --}}
            <div class="mb-3">
              <label class="form-label">Name</label>
              <input type="text" name="name" class="form-control" 
                     value="{{ old('name') }}" required>
              @error('name')
                <small class="text-danger">{{ $message }}</small>
              @enderror
            </div>

            {{-- Phone --}}
            <div class="mb-3">
              <label class="form-label">Phone</label>
              <input type="text" name="phone" class="form-control" 
                     value="{{ old('phone') }}" required>
              @error('phone')
                <small class="text-danger">{{ $message }}</small>
              @enderror
            </div>

            {{-- Email --}}
            <div class="mb-3">
              <label class="form-label">Email</label>
              <input type="email" name="email" class="form-control" 
                     value="{{ old('email') }}" required>
              @error('email')
                <small class="text-danger">{{ $message }}</small>
              @enderror
            </div>

            {{-- Password --}}
            <div class="mb-3">
              <label class="form-label">Password</label>
              <input type="password" name="password" class="form-control" required>
              @error('password')
                <small class="text-danger">{{ $message }}</small>
              @enderror
            </div>

            {{-- Role --}}
            <div class="mb-3">
              <label class="form-label">Role</label>
              <select name="role" class="form-select" required>
                <option value="" disabled selected>Select role</option>

                <option value="admin"   {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="doctor"  {{ old('role') == 'doctor' ? 'selected' : '' }}>Doctor</option>
                <option value="patient" {{ old('role') == 'patient' ? 'selected' : '' }}>Patient</option>
              </select>
              @error('role')
                <small class="text-danger">{{ $message }}</small>
              @enderror
            </div>

            {{-- Submit --}}
            <div class="d-grid">
              <button type="submit" class="btn btn-primary">Create User</button>
            </div>

          </form>
        </div>
      </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>
