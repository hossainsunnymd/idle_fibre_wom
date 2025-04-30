<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SessionAuthenticateMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $email=$request->session()->get('email','default');
        $userId=$request->session()->get('user_id','default');
        $count=User::where('email','=',$email)->count();
        if($email=="default" || $count==0){
            return redirect('/');
        }
        else{
            $request->headers->set('email',$email);
            $request->headers->set('user_id',$userId);
            return $next($request);
        }

    }
}
