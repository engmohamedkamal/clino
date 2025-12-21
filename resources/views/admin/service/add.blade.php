<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

  <title>Add Service</title>
</head>

<body>
  <div class="container">

    <form method="POST" action="{{ route('service.store') }}" novalidate enctype="multipart/form-data">
      @csrf
      <div class="mb-3">
        <label for="name" class="form-label">Service Name</label>
        <input type="text" class="form-control" id="name" name="name">
      </div>
      <div class="mb-3">
        <label for="description" class="form-label">Service description</label>
        <input type="text" class="form-control" id="description" name="description">
      </div>
      <div class="mb-3">
        <label class="form-label d-block">status</label>

        <div class="form-check form-check-inline">
          <input class="form-check-input" type="radio" name="status" id="status_active" value="1" {{ old('status') == 1 ? 'checked' : '' }}>
          <label class="form-check-label" for="status_active">
            Active
          </label>
        </div>

        <div class="form-check form-check-inline">
          <input class="form-check-input" type="radio" name="status" id="status_inactive" value="0" {{ old('status') == 0 ? 'checked' : '' }}>
          <label class="form-check-label" for="status_inactive">
            Inactive
          </label>
        </div>

        @error('status')
          <div class="text-danger mt-2">{{ $message }}</div>
        @enderror
      </div>

      <div class="mb-3">
        <label for="image" class="form-label">Service Image</label>
        <input type="file" class="form-control" id="image" name="image">
      </div>
      <button type="submit" class="btn btn-primary">Submit</button>
    </form>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
    crossorigin="anonymous"></script>
</body>

</html>