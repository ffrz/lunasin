@extends('report.layout', [
    'title' => $title,
])

@section('content')
  <table>
    <thead>
      <tr>
        <th>No</th>
        <th>Kategori</th>
        <th>Jumlah (Rp)</th>
      </tr>
    </thead>
    <tbody>
      @forelse ($items as $index => $item)
        <tr>
          <td align="right">{{ $index + 1 }}</td>
          <td>{{ $item->category_name }}</td>
          <td align="right">{{ format_number($item->total_amount) }}</td>
        </tr>
      @empty
        <tr>
          <td colspan="3" align="center">Tidak ada data</td>
        </tr>
      @endforelse
    </tbody>
    <tfoot>
      <tr>
        <td colspan="2" align="right"><strong>Total Keseluruhan:</strong></td>
        {{-- Calculate total sum of all items --}}
        <td align="right"><strong>{{ format_number($items->sum('total_amount')) }}</strong></td>
      </tr>
    </tfoot>
  </table>
@endsection
