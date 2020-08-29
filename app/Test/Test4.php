<?php
class Test4 {
    public $kurs=array();
    function kurs($country, $a){
        if($this->kurs==null){
            $this->kurs[$country]=$a;
        }else{
            foreach ($this->kurs as $key => $value) {
                if($key!=$country){
                    $this->kurs[$country]=$a;
                }else if($key==$country){
                    $this->kurs[$key]=$a;
                }
            }
        }
    }
    function konv($country, $b){
        foreach ($this->kurs as $key => $value) {
            if($key==$country){
                
                return "USD ".$b /$this->kurs[$country]."->($b/".$this->kurs[$country].")</br>";
                
            }
        }
        return "$country $b ->unknown";
    }
    function a(){
        var_dump($this->kurs);
    }
}
$test=new Test4();
$test->kurs("IDR",9000);
$test->kurs("CAD",400);
$test->kurs("JPY",300);
echo $test->konv("JPY",40000);
echo $test->konv("IDR",80000);
$test->kurs("IDR",9800);
echo $test->konv("IDR",80000);
echo $test->konv("MYR",80000);