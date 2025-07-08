<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class UserController extends Controller
{

    //user list page
    public function userPage(Request $request){
        $users=User::get();
        return Inertia::render('User/UserListPage',['users'=>$users]);
    }

    //user save page
    public function userSavePage(Request $request){
        $id=$request->query('id');
        $user=User::where('id','=',$id)->first();
        return Inertia::render('User/UserSavePage',['users'=>$user]);
    }

    //create user
    public function createUser(Request $request){
        $request->validate([
            'name'=>'required|string',
            'email'=>'required|email|unique:users',
            'password'=>'required|min:8',
            'mobile'=>'required',
            'role'=>'required',
        ]);

        $data=[
            'name'=>$request->name,
            'email'=>$request->email,
            'password'=>Hash::make($request->password),
            'mobile'=>$request->mobile,
            'role'=>$request->role,
        ];
        User::create($data);
        return redirect()->back()->with(['status'=>true,'message'=>'User created successfully','error'=>'']);
    }

    //update user
    public function updateUser(Request $request){
        $request->validate([
            'name'=>'required|string',
            'password'=>'required|min:8',
            'mobile'=>'required',
            'role'=>'required',
        ]);
        $data=[
            'name'=>$request->name,
            'password'=>Hash::make($request->password),
            'mobile'=>$request->mobile,
            'role'=>$request->role,
        ];
        $id=$request->id;
        User::where('id','=',$id)->update($data);
        return redirect()->back()->with(['status'=>true,'message'=>'User updated successfully','error'=>'']);
    }
    
    //delete user
    public function deleteUser(Request $request){

        $id=$request->id;
        $role = User::where('id','=',$id)->first()->role;
        if($role=='superadmin'){
            return redirect()->back()->with(['status'=>false,'message'=>'You do not have permission to delete user','error'=>'']);
        }
        else{

        User::where('id','=',$id)->delete();
        return redirect()->back()->with(['status'=>true,'message'=>'User deleted successfully','error'=>'']);
        };
    }

}
