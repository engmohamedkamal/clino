<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
  <title>Add About</title>
</head>

<body>
  <div class="container">
    <form method="POST" action="{{ route('about.store') }}" novalidate>
      @csrf
      <div class="mb-3">
        <label for="vision" class="form-label">Vision</label>
        <textarea name="vision" id="vision" cols="30" rows="10"></textarea>
      </div>
      <div class="mb-3">
        <label for="mission" class="form-label">Mission</label>
        <textarea name="mission" id="mission" cols="30" rows="10"></textarea>
      </div>
      <button type="submit" class="btn btn-primary">Submit</button>
    </form>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
    crossorigin="anonymous"></script>
</body>

</html>