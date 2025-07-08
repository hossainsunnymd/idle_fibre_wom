<?php

namespace App\Http\Controllers;

use Exception;
use Inertia\Inertia;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Models\InvoiceProduct;
use Illuminate\Support\Facades\DB;
use App\Services\InvoiceListService;

class InvoiceController extends Controller
{
    //sale page
    public function salePage(Request $request)
    {
        $customers = Customer::latest()->get();
        $products = Product::latest()->get();
        return Inertia::render('Sale/SalePage', ['customers' => $customers, 'products' => $products]);
    }

    //create invoice
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

    //list invoice
    public function listInvoice(InvoiceListService $invoiceListService, Request $request)
    {
        $data = $invoiceListService->getInvoiceList($request);
        return Inertia::render('Invoice/InvoiceListPage', [
            'list' => $data['list'],
            'customerList' =>   $data['customerList'],
            'total' => number_format($data['total'], 2),
            'fromDate' => $data['fromDate'],
            'toDate' => $data['toDate'],
            'totalByKg' => number_format($data['totalByKg'], 2),
            'totalByPc' => number_format($data['totalByPc'], 2),
        ]);
    }


    //delete invoice
    public function deleteInvoice(Request $request)
    {


        try {
            $role = $request->session()->get('role');
            if ($role == 'superadmin') {
                $invoiceId = $request->input('id');
                Invoice::where('id', '=', $invoiceId)->delete();
                return redirect()->route('listInvoice')->with(['status' => true, 'message' => 'Invoice deleted successfully']);
            } else {
                return redirect()->route('listInvoice')->with(['status' => false, 'message' => 'You do not have permission to delete invoice']);
            }
        } catch (Exception $e) {

            return redirect()->route('listInvoice')->with(['status' => false, 'message' => 'Something went wrong']);
        }
    }
}
