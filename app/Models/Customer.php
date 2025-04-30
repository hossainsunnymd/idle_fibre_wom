<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = ['name','address','mobile', 'unit', 'tin_number', 'bin_number', 'others'];

    use HasFactory;
}
