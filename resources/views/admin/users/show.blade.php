<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Users</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
      body {
        background-color: #f5f5f5;
      }
      .users-card {
        margin-top: 30px;
      }
    </style>
  </head>
  <body>

    <div class="container users-card">

      {{-- Alert success --}}
      @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
          {{ session('success') }}
          <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
      @endif

      <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0">Users</h3>
        <a href="{{ route('users.index') }}" class="btn btn-primary">Create User</a>
      </div>

      {{-- Search --}}
      <form method="GET" action="{{ route('users.index') }}" class="row g-2 mb-3">
        <div class="col-md-4">
          <input type="text" name="search" class="form-control"
                 placeholder="Search by name, email, phone"
                 value="{{ request('search') }}">
        </div>
        <div class="col-md-2 d-grid">
          <button type="submit" class="btn btn-outline-primary">Search</button>
        </div>
        @if(request('search'))
        <div class="col-md-2 d-grid">
          <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">Clear</a>
        </div>
        @endif
      </form>

      {{-- Table --}}
      <div class="card shadow-sm">
        <div class="card-body p-0">
          <div class="table-responsive">
            <table class="table table-striped table-hover mb-0 align-middle">
              <thead class="table-primary">
                <tr>
                  <th>#</th>
                  <th>Name</th>
                  <th>Phone</th>
                  <th>Email</th>
                  <th>Role</th>
                  <th style="width: 160px;">Actions</th>
                </tr>
              </thead>
              <tbody>
                @forelse($users as $user)
                  <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->phone }}</td>
                    <td>{{ $user->email }}</td>
                    <td>
                      <span class="badge
                        @if($user->role == 'admin') bg-danger
                        @elseif($user->role == 'doctor') bg-success
                        @else bg-secondary
                        @endif">
                        {{ ucfirst($user->role) }}
                      </span>
                    </td>
                    <td>
                      <a href="{{ route('users.edit', $user->id) }}" class="btn btn-sm btn-warning">Edit</a>

                      <form action="{{ route('users.destroy', $user->id) }}"
                            method="POST"
                            style="display:inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="btn btn-sm btn-danger"
                                onclick="return confirm('Are you sure you want to delete this user?')">
                          Delete
                        </button>
                      </form>
                    </td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="6" class="text-center text-muted py-3">
                      No users found.
                    </td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>

      {{-- Pagination --}}
     

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  </body>
</html>
