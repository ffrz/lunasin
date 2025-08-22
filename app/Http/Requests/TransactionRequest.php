<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Transaction;

class TransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // kalau perlu pakai policy, bisa diubah
    }

    public function rules(): array
    {
        return [
            'id'          => 'nullable|exists:transactions,id',
            'party_id'    => 'required|exists:parties,id',
            'category_id' => 'required|exists:transaction_categories,id',
            'datetime'    => 'required|date',
            'type'        => 'required|in:' . implode(',', array_keys(Transaction::Types)),
            'amount'      => 'required|numeric|min:0.01',
            'notes'       => 'nullable|string|max:255',
            'image'       => 'nullable|image|max:5120',
            'image_path'  => 'nullable|string',

        ];
    }

    public function messages(): array
    {
        return [
           'party_id.required'    => 'Pilih pihak wajib diisi.',
            'party_id.exists'      => 'Pihak yang dipilih tidak valid.',
            'category_id.required' => 'Kategori transaksi wajib diisi.',
            'category_id.exists'   => 'Kategori transaksi tidak valid.',
            'datetime.required'    => 'Tanggal transaksi wajib diisi.',
            'datetime.date'        => 'Tanggal transaksi harus format tanggal yang valid.',
            'type.required'        => 'Jenis transaksi wajib dipilih.',
            'type.in'              => 'Jenis transaksi tidak sesuai.',
            'amount.required'      => 'Jumlah transaksi wajib diisi.',
            'amount.numeric'       => 'Jumlah transaksi harus berupa angka.',
            'amount.min'           => 'Jumlah transaksi minimal Rp 0,01.',
            'notes.max'            => 'Catatan maksimal 255 karakter.',
            'image.image'          => 'File yang diunggah harus berupa gambar.',
            'image.max'            => 'Ukuran gambar maksimal 5 MB.',
        ];
    }
}
