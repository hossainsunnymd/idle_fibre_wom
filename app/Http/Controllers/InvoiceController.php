<?php

namespace App\Http\Controllers;

use App\Models\CompleteWorkOrder;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\InvoiceProduct;
use App\Models\Product;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class InvoiceController extends Controller
{
    public function createInvoice(Request $request)
    {

        DB::beginTransaction();
        try {
            $data = [
                'customer_id' => $request->input('cus_id'),
                'total' => $request->input('total'),
                'created_by' => $request->input('created_by'),
                'total_by_pc' => $request->input('total_by_pc'),
                'total_by_kg' => $request->input('total_by_kg'),
                'delivery_date' => $request->input('delivery_date'),
                'delivery_place' => $request->input('delivery_place'),
                'note' => $request->input('note'),

            ];

            $invoice = Invoice::create($data);

            $products = $request->input('products');
            foreach ($products as $product) {
                InvoiceProduct::create([
                    'invoice_id' => $invoice->id,
                    'product_id' => $product['id'],
                    'qty_by_kg' => $product['qty_by_kg'],
                    'qty_by_pc' => $product['qty_by_pc'],
                    'rate' => $product['rate'],
                    'order_price' => $product['order_price'],
                ]);
            }
            DB::commit();
            return redirect()->route('salePage')->with(['status' => true, 'message' => 'Invoice created successfully']);
        } catch (Exception $e) {
            DB::rollBack();
            return redirect()->route('salePage')->with(['status' => false, 'message' => 'Something went wrong']);
        }
    }

    public function listInvoice(Request $request)
    {
        $customerList = Customer::all();
        $list = Invoice::when($request->query('fromDate') && $request->query('toDate'), function ($query) use ($request) {
            $fromDate = date('Y-m-d', strtotime($request->fromDate));
            $toDate = date('Y-m-d', strtotime($request->toDate));
            $query->whereDate('created_at', '>=', $fromDate)->whereDate('created_at', '<=', $toDate);
        })->when($request->query('customerId'), function ($query) use ($request) {
            $query->where('customer_id', '=', $request->customerId);
        })->when($request->query('fromDate') && $request->query('toDate') && $request->query('customerId'), function ($query) use ($request) {
            $fromDate = date('Y-m-d', strtotime($request->fromDate));
            $toDate = date('Y-m-d', strtotime($request->toDate));
            $query->where('customer_id', '=', $request->customerId)->whereDate('created_at', '>=', $fromDate)->whereDate('created_at', '<=', $toDate);
        })->with('customer', 'invoiceProducts.product')
            ->latest()
            ->get();

        $total = Invoice::when($request->query('fromDate') && $request->query('toDate'), function ($query) use ($request) {
            $fromDate = date('Y-m-d', strtotime($request->fromDate));
            $toDate = date('Y-m-d', strtotime($request->toDate));
            $query->whereDate('created_at', '>=', $fromDate)->whereDate('created_at', '<=', $toDate);
        })->when($request->query('customerId'), function ($query) use ($request) {
            $query->where('customer_id', '=', $request->customerId);
        })->when($request->query('fromDate') && $request->query('toDate') && $request->query('customerId'), function ($query) use ($request) {
            $fromDate = date('Y-m-d', strtotime($request->fromDate));
            $toDate = date('Y-m-d', strtotime($request->toDate));
            $query->where('customer_id', '=', $request->customerId)->whereDate('created_at', '>=', $fromDate)->whereDate('created_at', '<=', $toDate);
        })->with('customer', 'invoiceProducts.product')
            ->sum('total');


        $totalByKg = Invoice::when($request->query('fromDate') && $request->query('toDate'), function ($query) use ($request) {
            $fromDate = date('Y-m-d', strtotime($request->fromDate));
            $toDate = date('Y-m-d', strtotime($request->toDate));
            $query->whereDate('created_at', '>=', $fromDate)->whereDate('created_at', '<=', $toDate);
        })->when($request->query('customerId'), function ($query) use ($request) {
            $query->where('customer_id', '=', $request->customerId);
        })->when($request->query('fromDate') && $request->query('toDate') && $request->query('customerId'), function ($query) use ($request) {
            $fromDate = date('Y-m-d', strtotime($request->fromDate));
            $toDate = date('Y-m-d', strtotime($request->toDate));
            $query->where('customer_id', '=', $request->customerId)->whereDate('created_at', '>=', $fromDate)->whereDate('created_at', '<=', $toDate);
        })->with('customer', 'invoiceProducts.product')
            ->sum('total_by_kg');

        $totalByPc = Invoice::when($request->query('fromDate') && $request->query('toDate'), function ($query) use ($request) {
            $fromDate = date('Y-m-d', strtotime($request->fromDate));
            $toDate = date('Y-m-d', strtotime($request->toDate));
            $query->whereDate('created_at', '>=', $fromDate)->whereDate('created_at', '<=', $toDate);
        })->when($request->query('customerId'), function ($query) use ($request) {
            $query->where('customer_id', '=', $request->customerId);
        })->when($request->query('fromDate') && $request->query('toDate') && $request->query('customerId'), function ($query) use ($request) {
            $fromDate = date('Y-m-d', strtotime($request->fromDate));
            $toDate = date('Y-m-d', strtotime($request->toDate));
            $query->where('customer_id', '=', $request->customerId)->whereDate('created_at', '>=', $fromDate)->whereDate('created_at', '<=', $toDate);
        })->with('customer', 'invoiceProducts.product')
            ->sum('total_by_pc');

        return Inertia::render('Invoice/InvoiceListPage', [
            'list' => $list,
            'customerList' => $customerList,
            'total' => $total,
            'fromDate' => $request->fromDate,
            'toDate' => $request->toDate,
            'totalByKg' => $totalByKg,
            'totalByPc' => $totalByPc
        ]);
    }

    public function deleteInvoice(Request $request)
    {


        try {
            $role = $request->session()->get('role');
            if ($role == 'superadmin') {
                $invoiceId = $request->input('id');
            Invoice::where('id', '=', $invoiceId)->delete();
            return redirect()->route('listInvoice')->with(['status' => true, 'message' => 'Invoice deleted successfully']);
            }
            else{
                return redirect()->route('listInvoice')->with(['status' => false, 'message' => 'You do not have permission to delete invoice']);
            }
            
        } catch (Exception $e) {

            return redirect()->route('listInvoice')->with(['status' => false, 'message' => 'Something went wrong']);
        }
    }
}
