<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceProduct;
use App\Models\Product;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function salePage(Request $request){
        $customers=Customer::latest()->get();
        $products=Product::latest()->get();
        return Inertia::render('Sale/SalePage',['customers'=>$customers,'products'=>$products]);
    }

}
