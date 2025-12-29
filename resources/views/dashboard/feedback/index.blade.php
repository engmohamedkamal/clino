@extends('layouts.dash')
@section('dash-content')
  <main class="main">

    <!-- Topbar -->
    <header class="topbar">
      <div class="d-flex align-items-center gap-2">
        <button class="btn icon-btn d-lg-none" type="button" data-bs-toggle="offcanvas"
          data-bs-target="#mobileSidebar" aria-controls="mobileSidebar">
          <i class="fa-solid fa-bars"></i>
        </button>
        <div class="page-title">Feedback</div>
      </div>
    </header>

    <!-- Content -->
    <section class="content-area">
      <div class="h-100 d-flex align-items-start justify-content-center pt-3 pt-md-4">
        <div class="appointment-card">
          <h3 class="appointment-title mb-4">Add Feedback</h3>

          {{-- SUCCESS --}}
          @if (session('success'))
            <div id="successAlert" class="alert alert-success mb-3">
              {{ session('success') }}
            </div>
          @endif

       
          <form action="{{ route('feedback.store') }}" method="POST" class="card p-4 shadow-sm" novalidate>
            @csrf

            <div class="mb-3">
              <label class="form-label appointment-label" for="comment">Comment</label>
              <textarea
                id="comment"
                name="comment"
                rows="5"
                class="form-control appointment-control appointment-textarea @error('comment') is-invalid @enderror"
                placeholder="Write your feedback here..."
              >{{ old('comment') }}</textarea>

              @error('comment')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <input type="hidden" name="user_id" value="{{ Auth::id() }}">

            <div class="mt-3 mt-md-4">
              <button type="submit" class="btn btn-primary btn-save-full">
                Save
              </button>
            </div>
          </form>

        </div>
      </div>
    </section>
  </main>

  {{-- Auto hide success after 3 seconds --}}
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      const el = document.getElementById('successAlert');
      if (!el) return;
      setTimeout(() => {
        el.classList.add('fade');
        el.classList.remove('show');
        el.style.display = 'none';
      }, 3000);
    });
  </script>
@endsection
