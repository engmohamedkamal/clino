<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

  <title>Edit Service</title>
</head>

<body>
  <div class="container">

    <form method="POST" action="{{ route('service.update', $service->id) }}" novalidate enctype="multipart/form-data">
      @csrf
      @method('PUT')
      <div class="mb-3">
        <label for="name" class="form-label">Service Name</label>
        <input type="text" class="form-control" value="{{ $service->name }}" id="name" name="name">
      </div>
      <div class="mb-3">
        <label for="description" class="form-label">Service description</label>
        <input type="text" class="form-control" value="{{ $service->description }}" id="description" name="description">
      </div>
      <div class="mb-3">
        <label class="form-label">Service Image</label>
        <div class="d-flex align-items-center mb-2">
          @if ($service && $service->image)
            <img src="{{ asset($service->image) }}" class="img-thumbnail me-3" style="width: 100px; height: auto;">
          @else
            <span class="text-muted me-3">No image uploaded</span>
          @endif
          <input type="file" name="image" class="form-control w-auto">
        </div>
        @error('image')
          <span class="text-danger">{{ $message }}</span>
        @enderror
      </div>

      <div class="mb-3">
        <label class="form-label d-block">Status</label>

        <div class="form-check form-check-inline">
          <input class="form-check-input" type="radio" name="status" id="status_active" value="1" {{ old('status', $service->status) == 1 ? 'checked' : '' }}>
          <label class="form-check-label" for="status_active">
            Active
          </label>
        </div>

        <div class="form-check form-check-inline">
          <input class="form-check-input" type="radio" name="status" id="status_inactive" value="0" {{ old('status', $service->status) == 0 ? 'checked' : '' }}>
          <label class="form-check-label" for="status_inactive">
            Inactive
          </label>
        </div>

        @error('status')
          <div class="text-danger mt-2">{{ $message }}</div>
        @enderror
      </div>

      <button type="submit" class="btn btn-primary">Submit</button>
    </form>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
    crossorigin="anonymous"></script>
</body>

</html>