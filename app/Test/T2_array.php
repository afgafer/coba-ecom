<?php
$a=[102,32,99,32,45,102,45,67,67,100,100];
$b['0']="0";
for ($i=0; $i <count($a) ; $i++) { 
    echo "1";
    foreach ($b as $key => $value) {
        echo "2";
        if($key!=$a[$i]){
            $b[$key]="0";
            echo "3";
            var_dump("kacau ".$b);
        }else if($key==$a[$i]){
            $b[$key]=1;
        }
    }
}
var_dump($b);

// $a=[102,32,99,32,45,102,45,67,67,100,100];
// for ($i=0; $i <count($a) ; $i++) { 
//     $b[]=$a[$i];
//     for ($j=0; $j < count($b); $j++) { 
//         if($b[$j]=)
//     }
// }