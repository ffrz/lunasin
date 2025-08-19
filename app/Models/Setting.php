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

use Illuminate\Support\Facades\Auth;

class Setting extends BaseModel
{
    protected $fillable = [
        'user_id',
        'key',
        'value',
        'lastmod_datetime',
        'lastmod_user_id',
        'lastmod_username',
    ];

    protected static $settings = [];
    protected static $isInitialized = [];

    private static function _init($userId)
    {
        if (!isset(static::$isInitialized[$userId])) {
            $items = static::where('user_id', $userId)->get();
            foreach ($items as $item) {
                static::$settings[$userId][$item->key] = $item->value;
            }
            static::$isInitialized[$userId] = true;
        }
    }

    public static function value($key, $default = null, $userId = null)
    {
        $userId = $userId ?? Auth::id(); // default to current user
        static::_init($userId);
        return static::$settings[$userId][$key] ?? $default;
    }

    public static function values($userId = null)
    {
        $userId = $userId ?? Auth::id();
        static::_init($userId);
        return static::$settings[$userId];
    }

    public static function setValue($key, $value, $userId = null)
    {
        $userId = $userId ?? Auth::id();
        static::updateOrCreate(
            ['user_id' => $userId, 'key' => $key],
            ['value' => $value]
        );
        static::$settings[$userId][$key] = $value;
    }
}
