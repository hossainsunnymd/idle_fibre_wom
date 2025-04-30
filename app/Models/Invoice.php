<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'total',
        'total_by_pc',
        'total_by_kg',
        'customer_id',
        'total_work_order',
        'created_by',
        'note',
        'delivery_date',
        'delivery_place'
    ];

    public function customer(){
        return $this->belongsTo(Customer::class);
    }

    public function invoiceProducts(){
        return $this->hasMany(InvoiceProduct::class);
    }
}
