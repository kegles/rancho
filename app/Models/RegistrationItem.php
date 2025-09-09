<?php

// app/Models/RegistrationItem.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class RegistrationItem extends Model {
  use SoftDeletes;
  protected $fillable = ['registration_id','product_id','qty','unit_price','subtotal'];
  public function registration(): BelongsTo { return $this->belongsTo(Registration::class); }
  public function product(): BelongsTo { return $this->belongsTo(Product::class); }
}
