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
  <h2>My Info</h2>

  @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif

  @if(!$info)
    <p>No info yet.</p>
    <a class="btn btn-primary" href="{{ route('my-info.create') }}">Add Info</a>
  @else
    <a class="btn btn-warning" href="{{ route('my-info.edit') }}">Edit</a>

    <div class="mt-3">
      @if($info->image)
        <img src="{{ asset('storage/'.$info->image) }}" style="max-width:200px;border-radius:8px;">
      @endif

      <ul class="mt-3">
        <li><b>Availability:</b> {{ $info->availability_schedule }}</li>
        <li><b>Gender:</b> {{ $info->gender }}</li>
        <li><b>DOB:</b> {{ $info->dob }}</li>
        <li><b>Specialization:</b> {{ $info->specialization }}</li>
        <li><b>License #:</b> {{ $info->license_number }}</li>
        <li><b>Address:</b> {{ $info->address }}</li>
      </ul>

      <div>
        <b>About:</b>
        <p>{{ $info->about }}</p>
      </div>
    </div>
  @endif
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
  </body>
</html>