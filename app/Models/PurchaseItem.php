<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Testing\Fluent\Concerns\Has;

class PurchaseItem extends Model
{
    use HasFactory;
    // define fillable fields
    protected $fillable = [
        'purchase_id',
        'product_id',
        'quantity',
        'unit_price',
        'subtotal',
    ];
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
