<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemSetting extends Model
{

    protected $table = 'system_settings';

    protected $fillable = [
        'key',
        'value'
    ];

    /**
     * Get value from key
     *
     * @param  string $key
     * @return
     */
    public static function get(string $key)
    {
        $found = self::where('key', '=', $key)->first();

        if ($found) {
            return $found->value;
        }

        return null;
    }
}
