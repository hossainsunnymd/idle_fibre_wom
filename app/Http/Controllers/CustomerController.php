<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Exception;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CustomerController extends Controller
{
    // Display list of customers
    public function customerPage(Request $request)
    {
        $customers = Customer::latest()->get();
        return Inertia::render('Customer/CustomerPage', ['customers' => $customers]);
    }

    // Display customer save page (create or edit)
    public function customerSavePage(Request $request)
    {
        $id = $request->query('id');
        $customer = Customer::find($id);
        return Inertia::render('Customer/CustomerSavePage', ['customer' => $customer]);
    }

    // Create a new customer
    public function createCustomer(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'address' => 'nullable|string',
            'unit' => 'nullable|string',
            'mobile' => 'nullable|string',
             // mobile is optional (nullable)
        ]);

        try {
            Customer::create([
                'name' => $request->input('name'),
                'address' => $request->input('address'),
                'unit' => $request->input('unit'),
                'mobile' => $request->input('mobile'), // mobile can be null
            ]);

            return redirect()->route('customerPage')->with(['status' => true, 'message' => 'Customer created successfully']);
        } catch (Exception $e) {
            return redirect()->route('customerSavePage')->with(['status' => false, 'message' => 'Customer creation failed']);
        }
    }

    // Update an existing customer
    public function updateCustomer(Request $request)
    {
        
        $request->validate([
            'name' => 'required|string',
            'address' => 'nullable|string',
            'unit' => 'nullable|string',
            'mobile' => 'nullable|string', // mobile is optional (nullable)
        ]);

        try {
            $customerId = $request->input('id');
            Customer::where('id', '=', $customerId)->update([
                'name' => $request->input('name'),
                'address' => $request->input('address'),
                'unit' => $request->input('unit'),
                'mobile' => $request->input('mobile'), // mobile can be null
            ]);

            return redirect()->route('customerPage')->with(['status' => true, 'message' => 'Customer updated successfully']);
        } catch (Exception $e) {
            return redirect()->route('customerSavePage', ['id' => $request->input('id')])
                             ->with(['status' => false, 'message' => 'Customer update failed']);
        }
    }

    // Delete a customer
    public function deleteCustomer(Request $request)
    {
        try {
            $customerId = $request->query('id');
            Customer::where('id', '=', $customerId)->delete();

            return redirect()->route('customerPage')->with(['status' => true, 'message' => 'Customer deleted successfully']);
        } catch (Exception $e) {
            return redirect()->route('customerPage')->with(['status' => false, 'message' => 'Customer deletion failed']);
        }
    }
}
