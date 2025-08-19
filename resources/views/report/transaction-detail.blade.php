@extends('report.layout', [
    'title' => $title,
])

@section('content')
  <table>
    <thead>
      <tr>
        <th>No</th>
        <th>Waktu</th>
        <th>Pihak</th>
        <th>Kategori</th>
        <th>Jumlah (Rp)</th>
      </tr>
    </thead>
    <tbody>
      @forelse ($items as $index => $item)
        <tr>
          <td align="right">{{ $index + 1 }}</td>
          <td>{{ format_datetime($item->datetime) }}</td>
          <td>{{ $item->party->name }}</td>
          <td>{{ $item->category->name }}</td>
          <td align="right">{{ format_number(abs($item->amount)) }}</td>
        </tr>
      @empty
        <tr>
          <td colspan="6" align="center">Tidak ada data</td>
        </tr>
      @endforelse
    </tbody>
  </table>
@endsection
