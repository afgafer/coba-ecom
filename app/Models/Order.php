<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $guarded=[];
    public function district(){
        return $this->belongsTo(District::class);
    }

    public function orderDetail(){
        return $this->hasMany(OrderDetail::class);
    }
    
    public function payment(){
        return $this->hasOne(Payment::class);
    }

    public function getStatusLabelAttribute(){
        if($this->status==0){
            return "<span class='badge badge-secondary'>Baru</span>";
        }else if($this->status==1){
            return "<span class='badge badge-primary'>Dikonfirmasi</span>";
        }else if($this->status==2){
            return "<span class='badge badge-info'>Proses</span>";
        }else if($this->status==3){
            return "<span class='badge badge-warning'>Dikirim</span>";
        }else if($this->status==4){
            return "<span class='badge badge-success'>Selesai</span>";
        }
    }
}
