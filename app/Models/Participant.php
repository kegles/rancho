<?php

// app/Models/Participant.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Participant extends Model {
  protected $fillable = [
    'name',
    'callsign',
    'city',
    'email',
    'phone',
    'category_code',
    'trade_role',
    'trade_donation_pledge'
  ];
  public function registrations(): HasMany { return $this->hasMany(Registration::class); }
}
