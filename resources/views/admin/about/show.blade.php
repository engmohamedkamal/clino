<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <title>About us</title>
</head>

<body class="container m-5">
    <table class="table">
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Vision</th>
                <th scope="col">Mission</th>
                <th scope="col">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($abouts as $about)

                <tr>
                    <th scope="row">{{ $loop->iteration }}</th>
                    <td>{{ $about->vision }}</td>
                    <td>{{ $about->mission }}</td>
                    <td>
                        <a href="{{ route('about.edit', $about->id) }}" class="btn btn-sm btn-warning me-1">Edit</a>
                        <form action="{{ route('about.destroy', $about->id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger"
                                onclick="return confirm('Are you sure?')">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach

        </tbody>
    </table>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
        crossorigin="anonymous"></script>
</body>

</html>