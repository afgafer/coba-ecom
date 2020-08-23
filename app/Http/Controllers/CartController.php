<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Product;
use App\Models\Province;
use App\Models\City;
use App\Models\District;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Customer;
use Illuminate\Support\Str;
use DB;

class CartController extends Controller
{
    function getCart(){
        $cart=json_decode(request()->cookie('cart'),true);
        if($cart == ''){
            $cart=[];
        }
        return $cart;
    }
    public function add(Request $request){
        $this->validate($request,[
            'product_id'=>'required|exists:products,id',
            'qty'=>'required|integer'
        ]);
        $cart=$this->getCart();

        if($cart && array_key_exists($request->produst_id,$cart)){
            $cart[$request->product_id]['qty']+=$request->qty;
        }else{
            $product=Product::find($request->product_id);

            $cart[$request->product_id]=[
                'qty'=>$request->qty,
                'product_id'=>$product->id,
                'name'=>$product->name,
                'price'=>$product->price,
                'image'=>$product->image,
            ];
        }

        $cookie=cookie('cart', json_encode($cart),288);
        return redirect()->back()->cookie($cookie);
    }

    public function index(){
        $cart=$this->getCart();
        $total=collect($cart)->sum(function($q){
            return $q['qty'] * $q['price'];
        });

        return view('cart.index', compact('cart', 'total'));
    }

    public function update(Request $request){
        $cart=$this->getCart();
        foreach ($request->product_id as $key => $row) {
            if($request->qty[$key]==0){
                unset($cart[$row]);
            }else{
                $cart[$row]['qty']=$request->qty[$key];
            }
        }
        $cookie=cookie('cart', json_encode($cart), 2800);
        return redirect()->back()->cookie($cookie);
    }
    public function checkout(){
        $provinces=Province::orderBy('created_at', 'DESC')->get();
        $cart=$this->getCart();
        $total=collect($cart)->sum(function($q){
            return $q['qty'] * $q['price'];
        });
        return view('cart.checkout',compact('provinces', 'cart', 'total'));
    }
    public function getCity(){
        $cities=City::where('province_id', request()->province_id)->get();
        //dd($cities);
        return response()->json(['status'=>'success', 'data'=>$cities]);
    }
    public function getDistrict(){
        $districts=District::where('city_id', request()->city_id)->get();
        return response()->json(['status'=>'success', 'data'=>$districts]);
    }
    public function processCheckout(Request $request){
        $this->validate($request, [
            'name'=>'required|string|max:50',
            'phone'=>'required',
            'email'=>'required|email',
            'address'=>'required|string',
            'province_id'=>'required|exists:provinces,id',
            'city_id'=>'required|exists:cities,id',
            'district_id'=>'required|exists:districts,id',
        ]);
        DB::beginTransaction();
        try{
            $customer=Customer::where('email', $request->email)->first();
            if(!auth()->check() && $customer){
                return redirect()->back->with(['error'=>'Silahkan login terlebih dahulu']);
            }
            $cart=$this->getCart();

            $total=collect($cart)->sum(function($q){
                return $q['qty'] * $q['price'];
            });
            $customer=Customer::create([
                'name'=>$request->name,
                'email'=>$request->email,
                'phone'=>$request->phone,
                'address'=>$request->address,
                'district_id'=>$request->district_id,
                'phone'=>false
            ]);
            $order=Order::create([
                'invoice'=>Str::random(4).'-'.time(),
                'customer_id'=>$customer->id,
                'name'=>$customer->name,
                'phone'=>$request->phone,
                'address'=>$request->address,
                'district_id'=>$request->district_id,
                'total'=>$total,
            ]);
            foreach ($cart as $row) {
                $product=Product::find($row['product_id']);
                OrderDetail::Create([
                    'order_id'=>$order->id,
                    'product_id'=>$row['product_id'],
                    'name'=>$row['name'],
                    'price'=>$row['price'],
                    'qty'=>$row['qty'],
                    'weight'=>$product->weight,
                ]);
                DB::commit();
                $cart=[];
                $cookie=cookie('cart', json_encode($cart), 2880);
                return redirect()->route('cart.finish',$order->invoice)->cookie($cookie);
            }
        }catch(\Exception $e){
            DB::rollback();
            return redirect()->back()->with(['error'=>$e->getMessage()]);
        }
    }
    public function checkoutFinish($invoice)
    {
        $order=Order::with(['district.city'])->where('invoice', $invoice)->first();
        return view('cart.checkout_finish', compact('order'));
    }
}
