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
