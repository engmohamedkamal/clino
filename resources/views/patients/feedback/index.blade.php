<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Edit Appointment</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <form action="{{ route('feedback.store') }}" method="POST" class="card p-4 shadow-sm" novalidate>
        @csrf

        <h4 class="mb-3 text-center">Add Feedback</h4>

        {{-- Comment --}}
        <div class="mb-3">
            <label class="form-label">Comment</label>
            <input type="text" name="comment" class="form-control" placeholder="Write your feedback..."
                value="{{ old('comment') }}" required>

            @error('comment')
                <small class="text-danger">{{ $message }}</small>
            @enderror

            @error('user_id')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        {{-- user_id hidden --}}
        <input type="hidden" name="user_id" value="{{ Auth::id() }}">

        {{-- Submit button --}}
        <button type="submit" class="btn btn-primary w-100">Submit Feedback</button>

        {{-- Success message --}}
        @if(session('success'))
            <div class="alert alert-success mt-3">{{ session('success') }}</div>
        @endif
    </form>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    </body>

</html>