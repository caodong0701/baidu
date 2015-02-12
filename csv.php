<?php

header('Content-Type:text/html;charset=utf-8');

$filename = "D:/sj/sf.csv";
$file = fopen($filename,"r");
for($i=0;$i<100;$i++){
    print_r(fgetcsv($file));
}
fclose($file);

?>
