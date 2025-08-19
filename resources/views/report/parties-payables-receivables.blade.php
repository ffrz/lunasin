@extends('report.layout', [
    'title' => $title,
])

@section('content')
  <table>
    <thead>
      <tr>
        <th>No</th>
        <th>Pihak</th>
        <th>Jenis</th>
        <th>Telepon</th>
        <th>Alamat</th>
        <th>Total (Rp)</th>
      </tr>
    </thead>
    <tbody>
      @forelse ($items as $index => $item)
        <tr>
          <td align="right">{{ $index + 1 }}</td>
          <td>{{ $item->name }}</td>
          <td>{{ \App\Models\Party::Types[$item->type] }}</td>
          <td>{{ $item->phone }}</td>
          <td>{{ $item->address }}</td>
          <td align="right">{{ format_number(abs($item->balance)) }}</td>
        </tr>
      @empty
        <tr>
          <td colspan="6" align="center">Tidak ada data</td>
        </tr>
      @endforelse
    </tbody>
  </table>
@endsection
