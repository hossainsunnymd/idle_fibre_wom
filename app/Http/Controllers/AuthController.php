<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
        //login page
       public function loginPage() {
        return Inertia::render('Auth/LoginPage');
    }

    //user login
    public function userLogin(Request $request) {

           $request->validate([
               'email'=>'required|email',
               'password'=>'required|min:8',
           ]);
           $count=User::where('email','=',$request->email)->first();
           if($count != null && Hash::check($request->password,$count->password)){

                $request->session()->put('email',$count->email);
                $request->session()->put('user_id',$count->id);
                $request->session()->put('name',$count->name);
                $request->session()->put('role',$count->role);
                $data=['message'=>'User login Successfully','status'=>true,'error'=>''];
               if($count->role=='superadmin' || $count->role=='admin'){
                return redirect('/invoice-page')->with( $data);
               }

           }else{

                return redirect()->back()->with(['message'=>'User login Fail','status'=>'fail','error'=>'something went wrong']);
           }
    }

    //user logout
    public function userLogout(Request $request) {
        $request->session()->flush();
        return redirect()->route('loginPage');
   }
}
