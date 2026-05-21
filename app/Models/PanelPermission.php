<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class PanelPermission extends Model
{
    protected $fillable = ['panel', 'key', 'label', 'is_hidden'];

    protected function casts(): array
    {
        return ['is_hidden' => 'boolean'];
    }

    /** Returns ['key' => true/false (is_hidden)] for all permissions, cached. */
    public static function allKeyed(): array
    {
        return Cache::remember('panel_permissions', 60, function () {
            return static::all()->pluck('is_hidden', 'key')->toArray();
        });
    }

    /** Call after any toggle to bust the cache. */
    public static function bustCache(): void
    {
        Cache::forget('panel_permissions');
    }
}
