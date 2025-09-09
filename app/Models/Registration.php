<?php

// app/Models/Registration.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Registration extends Model {
  use SoftDeletes;
  protected $fillable = [
    'participant_id','ticket_type','days','is_exempt',
    'base_price','total_price','eligible_draw','status',
    'reg_number','badge_letter',
  ];
  public function participant(): BelongsTo { return $this->belongsTo(Participant::class); }
  public function items(): HasMany { return $this->hasMany(RegistrationItem::class); }
    public function attendees()
    {
        return $this->hasMany(\App\Models\RegistrationAttendee::class);
    }
}
