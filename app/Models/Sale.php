<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\SaleItem;
class Sale extends Model
{
    protected $fillable = ['reference', 'total_brut', 'discount', 'total_net'];

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }
}
