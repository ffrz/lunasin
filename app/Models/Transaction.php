<?php

/**
 * MIT License
 *
 * Copyright (c) 2025 Fahmi Fauzi Rahman
 * GitHub: https://github.com/ffrz
 * Email: fahmifauzirahman@gmail.com
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'party_id',
        'category_id',
        'type',
        'datetime',
        'amount',
        'notes',
    ];

    /**
     * Transaction types.
     */
    const Type_LoanGiven = 'loan_given';
    const Type_ReceivablePayment = 'receivable_payment';
    const Type_LoanReceived = 'loan_received';
    const Type_PayablePayment = 'payable_payment';
    const Type_Adjustment = 'adjustment';

    const Types = [
        self::Type_LoanGiven => '(-) Memberi Pinjaman / Piutang', // Anda memberi pinjaman ke pihak lain
        self::Type_ReceivablePayment => '(+) Terima Bayar Piutang', // Anda menerima bayaran dari piutang Anda
        self::Type_LoanReceived => '(+) Menerima Pinjaman', // Anda menerima pinjaman dari pihak lain
        self::Type_PayablePayment => '(-) Bayar Utang', // Anda membayar utang Anda
        self::Type_Adjustment => '(+/-) Penyesuaian',
    ];

    public static function isPositiveTransaction($type): bool
    {
        return $type === self::Type_ReceivablePayment || $type == self::Type_LoanReceived; // Uang Masuk
    }

    public static function isNegativeTransaction($type): bool
    {
        return $type === self::Type_LoanGiven || $type == self::Type_PayablePayment; // Uang Keluar
    }

    protected function casts(): array
    {
        return [
            'party_id' => 'integer',
            'category_id' => 'integer',
            'datetime' => 'datetime',
            'type' => 'string',
            'amount' => 'decimal:2',
            'notes' => 'string',
            'created_by_uid' => 'integer',
            'updated_by_uid' => 'integer',
            'created_datetime' => 'datetime',
            'updated_datetime' => 'datetime',
        ];
    }

    public function getTypeLabelAttribute()
    {
        return self::Types[$this->type] ?? '-';
    }

    public function party()
    {
        return $this->belongsTo(Party::class, 'party_id');
    }

    public function category()
    {
        return $this->belongsTo(TransactionCategory::class, 'category_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_uid');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by_uid');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
