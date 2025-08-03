<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;

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
        self::Type_Company => 'Perusahaan',
    ];

    public static function activePartyCount()
    {
        return self::where('active', 1)->where('user_id', Auth::id())->count();
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
}
