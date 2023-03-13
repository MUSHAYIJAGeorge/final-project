<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use phpDocumentor\Reflection\Types\Null_;

class Adminmiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        $user = Auth::user();
        if ($user->user_type =='Superadmin' || 'admin' ||'retailer'||'customer') {
            return $next($request);
            return view('admin/dashboard');
        }elseif($user->user_type == Null || empty($user->user_type)){
            return Redirect::to('/');

        }
   else{
        //    exit("next");
        return "we are Really sorry";
            // return redirect('/dashbord')->with('status','Your not allowed to login to admin dashbord');
        }

    }
}
