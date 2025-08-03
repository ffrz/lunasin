<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
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
    const Type_Debt = 'debt';
    const Type_Credit = 'credit';
    const Type_Adjustment = 'adjustment';

    const Types = [
        self::Type_Debt => 'Uang Masuk',
        self::Type_Credit => 'Uang Keluar',
        self::Type_Adjustment => 'Penyesuaian',
    ];

    public static function isPositiveTransaction($type): bool
    {
        return $type === self::Type_Debt; // Uang Masuk
    }

    public static function isNegativeTransaction($type): bool
    {
        return $type === self::Type_Credit; // Uang Keluar
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
}
