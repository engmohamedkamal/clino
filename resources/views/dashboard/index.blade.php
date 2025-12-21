{{-- <h1>Welcome to the Dashboard</h1>
<a href="{{ route('appointment') }}">Add Appointment</a>
<a href="{{ route('appointment.show') }}">Show Appointment</a>

<a href="{{ route('users.store') }}">Add user</a>
<a href="{{ route('users.index') }}">Show users</a>


<a href="{{ route('feedback.form') }}">Add Feedback</a>


<a href="{{ route('patient-info.index') }}">Patients Info</a>
<a href="{{ route('patient-info.create') }}">Add Patient Info</a>
<a href="{{ route('patient-info.show', $patientInfo->id) }}">View Details</a>


@php
    $settings_exist = \App\Models\Setting::exists();
@endphp

@if(!$settings_exist)
    <a href="{{ route('settings.create') }}" class="btn btn-primary">
        Add Settings
    </a>
@else
    <a href="{{ route('settings.edit') }}" class="btn btn-warning">
        Edit Settings
    </a>
@endif --}}
<a href="{{ route('service') }}">Add Service</a>
<a href="{{ route('feedback.form') }}">Add Feedback</a>