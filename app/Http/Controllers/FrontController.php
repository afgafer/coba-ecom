<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;

class FrontController extends Controller
{
    public function index(){
        $products=Product::orderBy('created_at', 'DESC')->paginate(10);
        return view('ecom.index', compact('products'));
    }

    public function products(){
        $products=Product::orderBy('created_at', 'DESC')->paginate(12);
        $categories=Category::with(['child'])->withCount(['child'])->getParent()->orderBy('name', 'ASC')->get();
        return view('ecom.product', compact('products','categories'));
    }
    
    public function categoryProduct($slug){
        $products=Category::where('slug',$slug)->first()->product()->orderBy('created_at','DESC')->paginate(12);
        //dd($products);
        return view('ecom.product',compact('products'));
    }

    public function product($slug)
    {
        $product=Product::with(['category'])->where('slug',$slug)->first();
        return view('ecom.show', compact('product'));
    }

}
