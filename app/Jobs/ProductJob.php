<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Imports\ProductImprt;
use Illuminate\Support\Str;
use App\Product;
use File;

class ProductJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $category_id;
    protected $nameF;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($category_id, $nameF)
    {
        $this->$category_id=$category_id;
        $this->$nameF=$nameF;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $files=(new ProductImport)->toArray(storage_path('app/public/uploads'.$this->nameF));
        foreach ($files[0] as $row) {
            $explodeURL=explode('/',$row[4]);
            $explodeExtension=explode('.',end($explodeURL));
            $nameF=time().Str::random(6).'.'.end($explodeExtension);

            file_put_contents(storage_path('app/public/products').'/'.$nameF,file_get_contents($row[4]));
            Product::create([
                'name'=>$row[0],
                'slug'=>$row[0],
                'category_id'=>$this->category_id,
                'des'=>$row[1],
                'price'=>$row[2],
                'weight'=>$row[3],
                'image'=>$nameF,
                'status'=>true,
            ]);

            File::delete(storage_path('app/public/uploads/' . $this->filename));
        }
    }
}
