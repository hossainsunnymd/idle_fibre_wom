<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Inertia\Inertia;

class ProductController extends Controller
{


    public function productPage(Request $request){

        $products=Product::latest()->get();
        return Inertia::render('Product/ProductPage',['products'=>$products]);
    }

    public function productSavePage(Request $request){
        $productId=$request->query('id');
        $product=Product::where('id','=',$productId)->first();


        return Inertia::render('Product/ProductSavePage',['product'=>$product]);
    }
    public function createProduct(Request $request){
         try{

            $request->validate([
                'description' => 'required|string',
                'rate' => 'nullable|numeric|min:0',
                'size' => 'nullable|string',
                'weight'=>'nullable|string'
            ]);



            $data=[

               'decription'=>$request->input('description'),
               'rate'=>$request->input('rate'),
               'size'=>$request->input('size'),
               'weight'=>$request->input('weight'),

            ];

            Product::create($data);
            return redirect()->route('productSavePage')->with(['status'=>true,'message'=>'Product created successfully'],200);
         }catch(Exception $e){
            return redirect()->route('productSavePage')->with(['status'=>false,'message'=>'Something went wrong'],200);
         }

    }

    public function updateProduct(Request $request){
       try{
        $request->validate([
            'description' => 'required|string',
            'rate' => 'nullable|numeric|min:0',
            'size' => 'nullable|string',
            'weight'=>'nullable|string'
        ]);
        $data=[
            'decription'=>$request->input('description'),
            'rate'=>$request->input('rate'),
            'size'=>$request->input('size'),
            'weight'=>$request->input('weight'),
         ];

         Product::where('id','=',$request->input('id'))->update($data);

         return redirect()->route('productSavePage')->with(['status'=>true,'message'=>'Product updated successfully']);
       }catch(Exception $e){
           return redirect()->route('productSavePage') ->with(['status'=>false,'message'=>'Something went wrong']);
       }
    }

    public function deleteProduct(Request $request) {
        try {
            $productId = $request->input('id');
            
            // Delete the product from the database
            Product::where('id', $productId)->delete();
    
            return redirect()->route('productPage')->with(['status' => true, 'message' => 'Product deleted successfully']);
        } catch (Exception $e) {
            return redirect()->route('productPage')->with(['status' => false, 'message' => 'Something went wrong']);
        }
    }
    
    
}
