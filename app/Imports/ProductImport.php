<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class ProductImport implements ToCollection
{
    use Importable;
    /**
    * @param Collection $collection
    */
    // public function collection(Collection $collection)
    // {
    //     //   
    // }
    public function startRow():int{
        return 2;
    }
    public function chunkSize():int{
        return 100;
    }
}
