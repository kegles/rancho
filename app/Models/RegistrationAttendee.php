<?php

// app/Models/RegistrationAttendee.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegistrationAttendee extends Model
{
    protected $fillable = ['registration_id','role','name'];
}
