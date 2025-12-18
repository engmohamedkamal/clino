

<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <title>Hello, world!</title>
  </head>
  <body>
   <div class="container">
    {{-- Alerts --}}
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card shadow-sm">
        <div class="card-header">
            <h4 class="mb-0">Create Settings</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('settings.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" name="name" class="form-control"
                               value="{{ old('name') }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Slogan</label>
                        <input type="text" name="slogan" class="form-control"
                               value="{{ old('slogan') }}">
                    </div>

                    <div class="col-md-12 mb-3">
                        <label class="form-label">Vision</label>
                        <textarea name="vision" class="form-control" rows="3">{{ old('vision') }}</textarea>
                    </div>

                    <div class="col-md-12 mb-3">
                        <label class="form-label">Mission</label>
                        <textarea name="mission" class="form-control" rows="3">{{ old('mission') }}</textarea>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Facebook URL</label>
                        <input type="text" name="facebook" class="form-control"
                               value="{{ old('facebook') }}">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Instagram URL</label>
                        <input type="text" name="instagram" class="form-control"
                               value="{{ old('instagram') }}">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Twitter URL</label>
                        <input type="text" name="twitter" class="form-control"
                               value="{{ old('twitter') }}">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Phone</label>
                        <input type="text" name="phone" class="form-control"
                               value="{{ old('phone') }}">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control"
                               value="{{ old('email') }}">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Address</label>
                        <input type="text" name="address" class="form-control"
                               value="{{ old('address') }}">
                    </div>

                    <div class="col-md-12 mb-3">
                        <label class="form-label">Logo</label>
                        <input type="file" name="logo" class="form-control">
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Save Settings</button>
            </form>
        </div>
    </div>
</div>
    <!-- Optional JavaScript; choose one of the two! -->

    <!-- Option 1: Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

    <!-- Option 2: Separate Popper and Bootstrap JS -->
    <!--
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>
    -->
  </body>
</html>
