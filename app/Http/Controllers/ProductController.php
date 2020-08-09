<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use File;
use App\Jobs\ProductJob;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products=Product::with(['category'])->orderBy('created_at','DESC');
        if(request()->q!=''){
            $products=Product::whereRaw("name like %".request()->q."%")->orderBy('name','ASC');
        }
        $products=$products->paginate(10);
        return view('product.index',compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories=Category::orderBy('name','DESC')->get();
        return view('product.create',compact('categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request,[
            'name'=>'required|string|max:100',
            'des'=>'required',
            'category_id'=>'required|exists:categories,id',
            'price'=>'required|integer',
            'weight'=>'required|integer',
            'image'=>'required|image|mimes:pg,jpeg,jpg',
        ]);
        if ($request->hasFile('image')) {
            $file=$request->file('image');
            $fName="product_".$request->name."_".time().".".$file->getClientOriginalExtension();
            $file->storeAs('public/products',$fName);
        
            $product=Product::create([
                'name'=>$request->name,
                'slug'=>$request->name,
                'category_id'=>$request->category_id,
                'des'=>$request->des,
                'image'=>$fName,
                'price'=>$request->price,
                'weight'=>$request->weight,
                'status'=>$request->status,
            ]);
            return redirect()->route('product.index')->with(['success'=>"The product has been save"]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\models\Product  $product
     * @return \Illuminate\Http\Response
     */
    // public function show(Product $product)
    // {
    //     //
    // }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $product = Product::find($id); //AMBIL DATA PRODUK TERKAIT BERDASARKAN ID
        $categories = Category::orderBy('name', 'DESC')->get(); //AMBIL SEMUA DATA KATEGORI
        return view('products.edit', compact('product', 'categories')); //LOAD VIEW DAN PASSING DATANYA KE VIEW
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
    //VALIDASI DATA YANG DIKIRIM
        $this->validate($request, [
            'name' => 'required|string|max:100',
            'des' => 'required',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|integer',
            'weight' => 'required|integer',
            'image' => 'nullable|image|mimes:png,jpeg,jpg' //IMAGE BISA NULLABLE
        ]);

        $product = Product::find($id); //AMBIL DATA PRODUK YANG AKAN DIEDIT BERDASARKAN ID
        $filename = $product->image; //SIMPAN SEMENTARA NAMA FILE IMAGE SAAT INI
    
        //JIKA ADA FILE GAMBAR YANG DIKIRIM
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . Str::slug($request->name) . '.' . $file->getClientOriginalExtension();
            //MAKA UPLOAD FILE TERSEBUT
            $file->storeAs('public/products', $filename);
            //DAN HAPUS FILE GAMBAR YANG LAMA
            File::delete(storage_path('app/public/products/' . $product->image));
        }

    //KEMUDIAN UPDATE PRODUK TERSEBUT
        $product->update([
            'name' => $request->name,
            'des' => $request->des,
            'category_id' => $request->category_id,
            'price' => $request->price,
            'weight' => $request->weight,
            'image' => $filename
        ]);
        return redirect(route('product.index'))->with(['success' => 'Data Produk Diperbaharui']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        $product=Product::find($id);
        File::delete(storage_path('app/public/product'.$product->image));
        $product->delete();
        return redirect()->route()->with(['success'=>"The product has been delete"]);
    }

    public function massUploadForm()
    {
        $category = Category::orderBy('name', 'DESC')->get();
        return view('products.bulk', compact('category'));
    }

    public function massUpload(Request $request){
        $this->validate($request,[
            'category_id'=>'required|exist:categories,id',
            'file'=>'required|mimes:xlsx'
        ]);

        if ($request->hasFile('file')) {
            $file=$request->file('file');
            $nameF='productF_'.$request->file.'.'.$file->getClientOrigialExtesion();
            $file->storeAs('public/uploads',$nameF);

            ProductJob::dispatch($request->category_id, $nameF);
            return redirect()->back()->with(['success'=>'Upload product has been schedule']);
        }
    }
}
