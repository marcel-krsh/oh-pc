<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Fields that are mass assignable.
 *
 * @var array
 */
class Message extends Model
{
    //
    protected $fillable = ['message'];

    /**
     * A message belong to a user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
