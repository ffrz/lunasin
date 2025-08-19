@php
  if (!isset($subtitles)) {
      $subtitles = [];
  }

  // Format periode kalau tersedia
  $periodText = null;
  if (!empty($start) && !empty($end)) {
      $periodText = $start->translatedFormat('d F Y') . ' s/d ' . $end->translatedFormat('d F Y');
  }
@endphp

<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $title }}</title>
  <style>
    body {
      font-family: sans-serif;
      font-size: 10pt;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 10px;
    }

    th,
    td {
      border: 1px solid black;
      padding: 2px 5px;
    }

    th {
      background-color: #f2f2f2;
      text-align: center;
    }
  </style>
</head>

<body>
  <div class="a4-landscape">
    @php
      $logoPath = public_path('assets/img/app-logo.png');
    @endphp
    @if (file_exists($logoPath))
      <div class="header-logo" style="text-align: center;">
        <img src="{{ $logoPath }}" alt="Logo Perusahaan" style="width: 24px; height: auto; margin: 0 auto;" />
      </div>
    @endif

    <h4 style="margin:0;text-align:center;">{{ env('APP_DISPLAY_NAME', 'Lunasin') }}</h4>
    <h2 style="margin:0;text-align:center;">{{ $title }}</h2>

    @foreach ($subtitles as $subtitle)
      <h3 style="margin:0;text-align:center;">{{ $subtitle }}</h3>
    @endforeach

    @if ($periodText)
      <div style="text-align:center;font-size:11px;margin:5px 0;">
        Periode: <b>{{ $periodText }}</b>
      </div>
    @endif

    <div style="text-align:center;font-size:10px;font-weight:normal;">
      Dibuat oleh <b>{{ Auth::user()->email }}</b>
      pada {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y H:i:s') }}
      - {{ env('APP_NAME') }} v{{ env('APP_VERSION_STR') }}
    </div>

    @yield('content')
  </div>
</body>

</html>
