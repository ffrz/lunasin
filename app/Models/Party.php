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
}
