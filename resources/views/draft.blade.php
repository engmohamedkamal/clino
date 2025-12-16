<form method="POST" action="{{ route('logout') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-danger btn-sm">
                            Logout
                        </button>
</form>

<a href="{{ route('service') }}">Add Service</a>
<a href="{{ route('service.show') }}">Show Service</a>
<br>
<br>
<br>
<a href="{{ route('about') }}">Add About</a>
<a href="{{ route('about.show') }}">Show About</a>
<br>
<br>
<br>
<a href="{{ route('my-info.create') }}">Add Doctor info</a>
