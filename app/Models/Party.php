<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;

class Party extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'type',
        'address',
        'notes',
        'active',
        'balance',
    ];

    const Type_Personal   = 'personal';
    const Type_Company   = 'company';

    const Types = [
        self::Type_Personal   => 'Perorangan',
        self::Type_Company => 'Perusahaan',
    ];

    public static function activePartyCount()
    {
        return DB::select(
            "select count(0) as count from parties where active=1"
        )[0]->count;
    }

    public function created_by_user()
    {
        return $this->belongsTo(User::class, 'created_by_uid');
    }

    public function updated_by_user()
    {
        return $this->belongsTo(User::class, 'updated_by_uid');
    }
}
