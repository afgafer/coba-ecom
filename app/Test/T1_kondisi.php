<?php
for ($i=1; $i <=100 ; $i++) { 
    $a=$i%3;
    $b=$i%5;
    if ($a==0 && $b==0) {
        echo "snip-snap, ";
    }elseif ($a==0) {
        echo "snip, ";
    }elseif ($b==0) {
        echo "snap, ";
    }else{
        echo $i.", ";
    }
}