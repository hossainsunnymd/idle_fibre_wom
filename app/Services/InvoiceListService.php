<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Customer;

class InvoiceListService
{

    public function getInvoiceList($request)
    {
        $customerList = Customer::all();

        $fromDate = $request->query('fromDate') ? date('Y-m-d', strtotime($request->fromDate)) : null;
        $toDate = $request->query('toDate') ? date('Y-m-d', strtotime($request->toDate)) : null;
        $customerId = $request->query('customerId');

        $list = Invoice::when($fromDate && $toDate, function ($query) use ($fromDate, $toDate) {
            $query->whereDate('created_at', '>=', $fromDate)->whereDate('created_at', '<=', $toDate);
        })
            ->when($customerId, function ($query) use ($customerId) {
                $query->where('customer_id', $customerId);
            })
            ->with('customer', 'invoiceProducts.product')
            ->latest()
            ->get();

        // Total invoice amount
        $total = Invoice::when($fromDate && $toDate, function ($query) use ($fromDate, $toDate) {
            $query->whereDate('created_at', '>=', $fromDate)->whereDate('created_at', '<=', $toDate);
        })
            ->when($customerId, function ($query) use ($customerId) {
                $query->where('customer_id', $customerId);
            })
            ->sum('total');

        // Total in kg
        $totalByKg = Invoice::when($fromDate && $toDate, function ($query) use ($fromDate, $toDate) {
            $query->whereDate('created_at', '>=', $fromDate)->whereDate('created_at', '<=', $toDate);
        })
            ->when($customerId, function ($query) use ($customerId) {
                $query->where('customer_id', $customerId);
            })
            ->sum('total_by_kg');

        // Total in pieces
        $totalByPc = Invoice::when($fromDate && $toDate, function ($query) use ($fromDate, $toDate) {
            $query->whereDate('created_at', '>=', $fromDate)->whereDate('created_at', '<=', $toDate);
        })
            ->when($customerId, function ($query) use ($customerId) {
                $query->where('customer_id', $customerId);
            })
            ->sum('total_by_pc');

        return [
            'list' => $list,
            'customerList' => $customerList,
            'total' => $total,
            'fromDate' => $fromDate,
            'toDate' => $toDate,
            'totalByKg' => $totalByKg,
            'totalByPc' => $totalByPc
        ];
    }
}
