<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceProduct extends Model
{
    protected $fillable=['invoice_id','product_id','qty_by_kg','qty_by_pc','order_price','rate'];

    public function product(){
        return $this->belongsTo(Product::class);
    }
    public function invoice(){
        return $this->belongsTo(Invoice::class);
    }
}
