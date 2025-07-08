<?php

namespace App\Http\Controllers;

use Exception;
use Inertia\Inertia;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    //product list page
    public function productPage(Request $request)
    {

        $products = Product::latest()->get();
        return Inertia::render('Product/ProductPage', ['products' => $products]);
    }

    //product list page
    public function productSavePage(Request $request)
    {
        $productId = $request->query('id');
        $product = Product::where('id', '=', $productId)->first();


        return Inertia::render('Product/ProductSavePage', ['product' => $product]);
    }

    //create product
    public function createProduct(Request $request)
    {

        $validation = Validator::make($request->all(), [
            'description' => 'required|string',
            'rate' => 'nullable|numeric|min:0',
        ]);

        if ($validation->fails()) {
            return redirect()->back()->with(['error' => $validation->errors()]);
        }

        try {

            $data = [

                'decription' => $request->input('description'),
                'rate' => $request->input('rate'),
                'size' => $request->input('size'),
                'weight' => $request->input('weight'),

            ];

            Product::create($data);
            return redirect()->route('productSavePage')->with(['status' => true, 'message' => 'Product created successfully'], 200);
        } catch (Exception $e) {
            return redirect()->route('productSavePage')->with(['status' => false, 'message' => 'Something went wrong'], 200);
        }
    }

    //update product
    public function updateProduct(Request $request)
    {

        $validation = Validator::make($request->all(), [
            'description' => 'required|string',
            'rate' => 'nullable|numeric|min:0',
        ]);

        if ($validation->fails()) {
            return redirect()->back()->with(['error' => $validation->errors()]);
        }

        try {
            $data = [
                'decription' => $request->input('description'),
                'rate' => $request->input('rate'),
                'size' => $request->input('size'),
                'weight' => $request->input('weight'),
            ];

            Product::where('id', '=', $request->input('id'))->update($data);

            return redirect()->route('productSavePage')->with(['status' => true, 'message' => 'Product updated successfully']);
        } catch (Exception $e) {
            return redirect()->route('productSavePage')->with(['status' => false, 'message' => 'Something went wrong']);
        }
    }

    //delete product
    public function deleteProduct(Request $request)
    {
        try {
            $productId = $request->input('id');
            Product::where('id', $productId)->delete();
            return redirect()->route('productPage')->with(['status' => true, 'message' => 'Product deleted successfully']);
        } catch (Exception $e) {
            return redirect()->route('productPage')->with(['status' => false, 'message' => 'Something went wrong']);
        }
    }
}
