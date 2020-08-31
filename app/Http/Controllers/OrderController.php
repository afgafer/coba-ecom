<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Models\Payment;
use Carbon\Carbon;
use DB;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $orders=Order::where('customer_id', auth()->guard('customer')->user()->id)->orderBy('created_at', 'DESC')->paginate(10);
        return view('order.index', compact('orders'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function show($invoice)
    {
        $order=Order::with(['district.city.province', 'orderDetail', 'orderDetail.product', 'payment'])
        ->where('invoice', $invoice)->first();
        return view('order.show', compact('order'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function edit(Order $order)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Order $order)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Order  $order
     * @return \Illuminate\Http\Response
     */
    public function destroy(Order $order)
    {
        //
    }
    public function paymentForm(){
        return view('order.payment');
    }

    public function payment(Request $request){
        $this->validate($request,[
            'invoice'=>'required|exists:orders,invoice',
            'name'=>'required|string',
            'account'=>'required|string',
            'date'=>'required',
            'amount'=>'required|integer',
            'proof'=>'required|image|mimes:jpg,png,jpeg',
        ]);
        DB::beginTransaction();
        try {
            $order=Order::where('invoice', $request->invoice)->first();
            if($order->status==0 && $request->hasFile('proof')){
                $file=$request->file('proof');
                $fileName=time()."_".$request->invoice."_".".".$file->getClientOriginalExtension();
                $file->storeAs('public/payment', $fileName);

                Payment::create([
                    'order_id'=>$order->id,
                    'name'=>$request->name,
                    'account'=>$request->account,
                    'date'=>Carbon::parse($request->date)->format('Y-m-d'),
                    'amount'=>$request->amount,
                    'proof'=>$fileName,
                    'status'=>false,
                ]);

                $order->update(['status'=>'1']);
                DB::commit();
                return redirect()->back()->with(['success'=>'Pesanan dikonfirmasi']);
            }
            return redirect()->back()->with(['error'=>'Error, upload bukti transfer failed']);
        } catch (\Throwable $e) {
            DB::rollback();
            return redirect()->back()->with(['error' => $e->getMessage()]);
        }        
    }
}
