<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Product;

class CartController extends Controller
{
    public function add(Request $request){
        $this->validate($request,[
            'product_id'=>'required|exists:products,id',
            'qty'=>'required|integer'
        ]);
        $carts=json_decode($request->cookie('dw-carts'), true);

        if($carts && array_key_exists($request->produst_id,$carts)){
            $carts[$request->product_id]['qty']+=$request->qty;
        }else{
            $product=Product::find($request->product_id);

            $carts[$request->product_id]=[
                'qty'=>$request->qty,
                'product_id'=>$product->id,
                'name'=>$product->name,
                'price'=>$product->price,
                'image'=>$product->image,
            ];
        }

        $cookie=cookie('carts', json_encode($carts),288);
        return redirect()->back()->cookie($cookie);
    }

    public function index(){
        $carts=json_decode(request()->cookie('carts'), true);
        $total=collect($carts)->sum(function($q){
            return $q['qty'] * $q['price'];
        });

        return view('cart.index', compact('carts', 'total'));
    }

    public function update(Request $request){
        $carts= json_decode(request()->cookie('carts'), true);
        foreach ($request->product_id as $key => $row) {
            if($request->qty[$key]==0){
                unset($carts[$row]);
            }else{
                $carts[$row]['qty']=$request->qty[$key];
            }
        }
        $cookie=cookie('carts', json_encode($carts), 2800);
        return redirect()->back()->cookie($cookie);
    }
}
