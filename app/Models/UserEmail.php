<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class UserEmail extends Model
{
  public $timestamps = true;

  protected $guarded = ['id'];

  public function user()
  {
    return $this->hasOne(User::class, 'id', 'user_id');
  }

  public function email_address()
  {
    return $this->hasOne(EmailAddress::class, 'id', 'email_address_id');
  }

  public function scopeAllita($query)
  {
    return $query->where('devco', 0)->orWhereNull('devco');
  }

}
