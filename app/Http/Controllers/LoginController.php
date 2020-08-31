<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Order;

class LoginController extends Controller
{
    public function loginV(){
        if(auth()->guard('customer')->check()) return redirect()->route('customer.dashboard');
        return view('ecom.login');
    }

    public function login(Request $request){
        $this->validate($request,[
            'email'=>'required|email|exists:customers,email',
            'password'=>'required|string'
        ]);

        $auth=$request->only('email', 'password');
        $auth['status']=1;
        
        if(auth()->guard('customer')->attempt($auth)){
            return redirect()->intended(route('customer.dashboard'));
        }
        return redirect()->back()->with(['error'=>'Email / Password salah']);
    }

    public function dashboard(){
        $orders=Order::selectRaw("COALESCE(SUM(CASE WHEN status=0 THEN total END), 0) AS pending,
            COALESCE(COUNT(CASE WHEN status=3 THEN total END), 0) AS shipping,
            COALESCE(COUNT(CASE WHEN status=4 THEN total END), 0) AS complete
            ")->where('customer_id', auth()->guard('customer')->user()->id)->get();
        return view('ecom.dashboard', compact('orders'));
    }

    public function logout(){
        auth()->guard('customer')->logout();
        return redirect(route('customer.login'));
    }
}
