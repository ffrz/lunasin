@extends('export.layout', [
    'title' => $title,
])

@section('content')
  <table>
    <thead>
      <tr>
        <th>No</th>
        <th>Nama Kategori</th>
      </tr>
    </thead>
    <tbody>
      @forelse ($items as $index => $item)
        <tr>
          <td align="right">{{ $index + 1 }}</td>
          <td>{{ $item->name }}</td>
        </tr>
      @empty
        <tr>
          <td colspan="2" align="center">Tidak ada rekaman</td>
        </tr>
      @endforelse
    </tbody>
  </table>
@endsection
