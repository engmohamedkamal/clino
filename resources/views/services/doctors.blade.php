{{-- resources/views/dashboard/services/doctors.blade.php --}}

@extends('layouts.dash')
@section('dash-content')

<link rel="stylesheet" href="{{ asset('css/card.css') }}">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet"/>

@php
  use Carbon\Carbon;

  /**
   * Build next slots from availability_schedule JSON:
   * schedule = [{day:"Mon", from:"13:00", to:"17:00"}, ...]
   * returns array of at most 3 labels like: "Tom, 9:00 AM"
   */
  function buildNextSlots($schedule, $limit = 3) {
    if (!is_array($schedule)) return [];

    $map = [
      'Sun' => Carbon::SUNDAY,
      'Mon' => Carbon::MONDAY,
      'Tue' => Carbon::TUESDAY,
      'Wed' => Carbon::WEDNESDAY,
      'Thu' => Carbon::THURSDAY,
      'Fri' => Carbon::FRIDAY,
      'Sat' => Carbon::SATURDAY,
    ];

    $now = Carbon::now();
    $items = [];

    foreach ($schedule as $row) {
      if (!is_array($row)) continue;

      $day  = $row['day']  ?? null;
      $from = $row['from'] ?? null;
      $to   = $row['to']   ?? null;

      if (!$day || !$from || !$to) continue;

      $day = ucfirst(strtolower(substr($day, 0, 3)));
      if (!isset($map[$day])) continue;

      try {
        $fromT = Carbon::createFromFormat('H:i', $from);
        $toT   = Carbon::createFromFormat('H:i', $to);
      } catch (\Throwable $e) {
        continue;
      }

      // next occurrence for that weekday
      $nextDate = $now->copy();
      $targetDow = $map[$day];

      if ($nextDate->dayOfWeek === $targetDow) {
        // same day: لو الوقت لسه قبل نهاية الشيفت اعتبره Today
        $endToday = $nextDate->copy()->setTimeFrom($toT);
        if ($now->lt($endToday)) {
          // today ok
        } else {
          $nextDate->addDays(7);
        }
      } else {
        // go to next weekday
        $nextDate->next($targetDow);
      }

      // slot1 = from
      $slot1 = $nextDate->copy()->setTimeFrom($fromT);

      // slot2 = from + 2 hours (لو داخل الرينج) وإلا نخليها near end
      $slot2 = $slot1->copy()->addHours(2);
      $shiftEnd = $nextDate->copy()->setTimeFrom($toT);
      if ($slot2->gte($shiftEnd)) {
        $slot2 = $shiftEnd->copy()->subHours(1);
        if ($slot2->lte($slot1)) $slot2 = null;
      }

      $items[] = $slot1;
      if ($slot2) $items[] = $slot2;
    }

    // sort & unique
    $items = collect($items)
      ->filter()
      ->sort()
      ->unique(fn($d) => $d->format('Y-m-d H:i'))
      ->values()
      ->take($limit)
      ->all();

    // labels like screenshot: Today / Tom / Wed ...
    $labels = [];
    foreach ($items as $d) {
      $dayLabel =
        $d->isToday() ? 'Today' :
        ($d->isTomorrow() ? 'Tom' : $d->format('D'));

      $labels[] = $dayLabel . ', ' . $d->format('g:i A');
    }

    return $labels;
  }
@endphp

<main class="main">
  <header class="topbar">
    <div class="d-flex align-items-center gap-2">
      <button class="btn icon-btn d-lg-none" type="button" data-bs-toggle="offcanvas"
        data-bs-target="#mobileSidebar" aria-controls="mobileSidebar">
        <i class="fa-solid fa-bars"></i>
      </button>
      <div class="page-title">Doctors</div>
    </div>
  </header>

  <section class="content-area">
    <div class="container py-3">
      <div class="row g-4">

        @foreach($doctors as $doc)
          @php
            $img  = $doc->image ? asset('storage/'.$doc->image) : asset('images/doctor-placeholder.png');
            $name = $doc->user->name ?? 'Doctor';

            $specArr = is_array($doc->Specialization) ? $doc->Specialization : [];
            $specTitle = count($specArr) ? $specArr[0] : 'General Dentist';

            $exp = $doc->experience_years ?? 8; // لو مش عندك حقل خبرة، سيبها placeholder

            $desc = $doc->about
              ? \Illuminate\Support\Str::limit(strip_tags($doc->about), 95)
              : 'Expert in dental care and oral surgery. Dedicated to restoring smiles with the latest...';

            $rating = $doc->rating ?? 4.8; // لو مش عندك rating، سيبها ثابت

            $schedule = $doc->availability_schedule ?? [];
            $slots = buildNextSlots($schedule, 3);
          @endphp

          <div class="col-12 col-md-6 col-lg-4">
            <article class="dd-card">

              <div class="dd-card-head">
                <div class="dd-avatar">
                  <img src="{{ $img }}" alt="doctor">
                </div>

                <div class="dd-main">
                  <div class="dd-name">Dr. {{ $name }}</div>
                  <div class="dd-role">{{ $specTitle }}</div>
                  {{-- <div class="dd-exp">{{ $exp }} Years Experience</div> --}}
                </div>

                <div class="dd-rate">
                  <i class="fa-solid fa-star"></i>
                  <span>{{ number_format((float)$rating, 1) }}</span>
                </div>
              </div>

              <p class="dd-desc">{{ $desc }}</p>

              <div class="dd-slots-title">NEXT AVAILABLE SLOTS</div>

              <div class="dd-slots">
                @if(count($slots))
                  @foreach($slots as $i => $label)
                    <span class="dd-chip {{ $i === 1 ? 'is-active' : '' }}">
                      {{ $label }}
                    </span>
                  @endforeach
                @else
                  <span class="dd-chip is-muted">No slots</span>
                @endif
              </div>

              <a
                class="dd-btn"
                href="{{ route('appointment.index', ['doctor' => $doc->user_id]) }}"
              >
                <i class="fa-regular fa-calendar-check"></i>
                <span>Book Appointment</span>
              </a>

            </article>
          </div>
        @endforeach

      </div>
    </div>
  </section>
</main>

@endsection
