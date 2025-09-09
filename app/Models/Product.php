<?php

// app/Models/Product.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model {
  protected $fillable = ['sku','name','price','is_child_half','active'];
}
