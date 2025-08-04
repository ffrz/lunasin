@extends('export.layout', [
    'title' => $title,
])

@section('content')
  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>Waktu</th>
        <th>Pihak</th>
        <th>Jenis</th>
        <th>Kategori</th>
        <th>Jumlah</th>
        <th>Keterangan</th>
      </tr>
    </thead>
    <tbody>
      @forelse ($items as $item)
        <tr>
          <td align="right">{{ $item->id }}</td>
          <td>{{ format_datetime($item->datetime) }}</td>
          <td>{{ $item->party?->name }}</td>
          <td>{{ App\Models\Transaction::Types[$item->type] }}</td>
          <td>{{ $item->category?->name }}</td>
          <td align="right">{{ format_number($item->amount) }}</td>
          <td>{{ $item->notes }}</td>
        </tr>
      @empty
        <tr>
          <td colspan="7" align="center">Tidak ada rekaman</td>
        </tr>
      @endforelse
    </tbody>
  </table>
@endsection
