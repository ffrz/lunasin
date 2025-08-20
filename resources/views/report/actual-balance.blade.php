@extends('report.layout', [
    'title' => $title,
])

@section('content')
  <table class="summary-table">
    <thead>
      <tr>
        <th>Keterangan</th>
        <th>Jumlah (Rp.)</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td><strong>Total Piutang (Pihak Lain kepada Anda):</strong></td>
        <td align="right"> {{ format_number($total_receivables) }}</td>
      </tr>
      <tr>
        <td><strong>Total Utang (Anda kepada Pihak Lain):</strong></td>
        <td align="right"> {{ $total_payables != 0 ? '-' : '' }}{{ format_number($total_payables) }}</td>
      </tr>
      <tr>
        <td><strong>Total Saldo Keseluruhan:</strong></td>
        <td align="right"> {{ format_number($total_balance) }}</td>
      </tr>
    </tbody>
  </table>

  <style>
    /* Styling dasar untuk tabel ringkasan */
    .summary-table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }

    .summary-table td,
    th {
      padding: 8px;
      border: 1px solid #ddd;
    }

    .summary-table strong {
      font-weight: bold;
    }
  </style>
@endsection
