@extends('export.layout', [
    'title' => $title,
])

@section('content')
  <table>
    <thead>
      <tr>
        <th>No</th>
        <th>Jenis</th>
        <th>Nama</th>
        <th>Telepon</th>
        <th>Alamat</th>
        <th>Status</th>
        <th>Utang/Piutang (Rp.)</th>
        <th>Catatan</th>
      </tr>
    </thead>
    <tbody>
      @forelse ($items as $index => $item)
        <tr>
          <td align="right">{{ $index + 1 }}</td>
          <td>{{ App\Models\Party::Types[$item->type] }}</td>
          <td>{{ $item->name }}</td>
          <td>{{ $item->phone }}</td>
          <td>{{ $item->address }}</td>
          <td>{{ $item->active ? 'Aktif' : 'Non Aktif' }}</td>
          <td align="right">{{ format_number($item->balance) }}</td>
          <td>{{ $item->notes }}</td>
        </tr>
      @empty
        <tr>
          <td colspan="8" align="center">Tidak ada data</td>
        </tr>
      @endforelse
    </tbody>
  </table>
@endsection
