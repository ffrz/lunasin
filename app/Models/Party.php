<?php

/**
 * MIT License
 * 
 * Copyright (c) 2025 Fahmi Fauzi Rahman
 * See LICENSE file in the project root for full license information.
 * 
 * GitHub: https://github.com/ffrz
 * Email: fahmifauzirahman@gmail.com
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Party extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'phone',
        'type',
        'address',
        'notes',
        'active',
        'balance',
    ];

    protected $casts = [
        'type' => 'string',
        'active' => 'boolean',
    ];

    const Type_Personal   = 'personal';
    const Type_Company   = 'company';

    const Types = [
        self::Type_Personal   => 'Perorangan',
        self::Type_Company => 'Perusahaan / Instansi',
    ];

    public static function activePartyCount()
    {
        return self::where('active', 1)->count();
    }

    public function created_by_user()
    {
        return $this->belongsTo(User::class, 'created_by_uid');
    }

    public function updated_by_user()
    {
        return $this->belongsTo(User::class, 'updated_by_uid');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public static function isUsedInTransaction($id)
    {
        return Transaction::where('party_id', $id)->exists();
    }
}
