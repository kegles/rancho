<?php

// app/Models/Product.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model {
    protected $fillable = ['sku','name','price','is_child_half','sort_order','active'];

    protected $casts = [
        'price' => 'integer', // centavos
        'is_child_half' => 'boolean',
        'sort_order' => 'integer',
        'active' => 'boolean',
    ];


    // Atributos auxiliares para formulários (R$)
    public function getPriceBrlAttribute(): string
    {
        return number_format($this->price / 100, 2, ',', '.');
    }


    public function setPriceBrlAttribute($value): void
    {
        // aceita "12,34" ou "12.34"
        $norm = str_replace(['.',' '],'', (string)$value);
        $norm = str_replace(',','.', $norm);
        $float = is_numeric($norm) ? (float)$norm : 0.0;
        $this->attributes['price'] = (int) round($float * 100);
    }

}
