@extends('export.layout', [
    'title' => $title,
])

@section('content')
  <table>
    <thead>
      <tr>
        <th>No</th>
        <th>Username</th>
        <th>Nama Lengkap</th>
        <th>Role</th>
        <th>Status</th>
      </tr>
    </thead>
    <tbody>
      @forelse ($items as $index => $item)
        <tr>
          <td align="right">{{ $index + 1 }}</td>
          <td>{{ $item->email }}</td>
          <td>{{ $item->name }}</td>
          <td>{{ \App\Models\User::Roles[$item->role] }}</td>
          <td>{{ $item->active ? 'Aktif' : 'Non Aktif' }}</td>
        </tr>
      @empty
        <tr>
          <td colspan="5" align="center">Tidak ada data</td>
        </tr>
      @endforelse
    </tbody>
  </table>
@endsection
