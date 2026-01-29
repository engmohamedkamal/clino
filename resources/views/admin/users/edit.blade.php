<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Edit User</title>
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
            <button class="btn icon-btn d-lg-none" type="button" data-bs-toggle="offcanvas"
        data-bs-target="#mobileSidebar" aria-controls="mobileSidebar">
        <i class="fa-solid fa-bars"></i>
      </button>
          <h4 class="mb-4 text-center">Edit User</h4>

          <form action="{{ route('users.update', $user->id) }}" method="POST">
              @csrf
              @method('PUT')

              {{-- Name --}}
              <div class="mb-3">
                <label class="form-label">Name</label>
                <input 
                    type="text" 
                    name="name" 
                    class="form-control" 
                    value="{{ old('name', $user->name) }}" 
                    required>
                @error('name')
                  <small class="text-danger">{{ $message }}</small>
                @enderror
              </div>

              {{-- Phone --}}
              <div class="mb-3">
                <label class="form-label">Phone</label>
                <input 
                    type="text" 
                    name="phone" 
                    class="form-control" 
                    value="{{ old('phone', $user->phone) }}" 
                    required>
                @error('phone')
                  <small class="text-danger">{{ $message }}</small>
                @enderror
              </div>

              {{-- Email --}}
              <div class="mb-3">
                <label class="form-label">Email</label>
                <input 
                    type="email" 
                    name="email" 
                    class="form-control" 
                    value="{{ old('email', $user->email) }}" 
                    required>
                @error('email')
                  <small class="text-danger">{{ $message }}</small>
                @enderror
              </div>

              {{-- Change Password (Optional) --}}
              <div class="mb-3">
                <label class="form-label">New Password (optional)</label>
                <input 
                    type="password" 
                    name="password" 
                    class="form-control">
                @error('password')
                  <small class="text-danger">{{ $message }}</small>
                @enderror
                <small class="text-muted">Leave empty if you don't want to change it.</small>
              </div>

              {{-- Role --}}
              <div class="mb-3">
                <label class="form-label">Role</label>
                <select name="role" class="form-select" required>

                  <option value="admin"   {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                  <option value="doctor"  {{ old('role', $user->role) == 'doctor' ? 'selected' : '' }}>Doctor</option>
                  <option value="patient" {{ old('role', $user->role) == 'patient' ? 'selected' : '' }}>Patient</option>

                </select>
                @error('role')
                  <small class="text-danger">{{ $message }}</small>
                @enderror
              </div>

              {{-- Buttons --}}
              <div class="d-flex justify-content-between">
                <a href="{{ route('users.index') }}" class="btn btn-secondary">Back</a>
                <button type="submit" class="btn btn-primary">Update User</button>
              </div>

          </form>

        </div>
      </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>
