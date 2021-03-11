<?php

function cmp($a, $b)
{
   // 以成人口罩數量進行由大至小排序

    if (mb_strlen($a[2], "UTF-8") != strlen($a[2])) {
        return -1 ;
    }

    if ( mb_strlen($b[2], "UTF-8") != strlen($b[2]) ) {
        return 1 ;
    } 

    if ((int)$a[2] == (int)$b[2]) {
        return 0 ;
    }
    return( (int)$a[2] > (int)$b[2] ) ? -1 : 1 ;
}

function run($input)
{

    require_once('vendor/autoload.php') ;
    $climate = new League\CLImate\CLImate();

    $csvData = file_get_contents('https://data.nhi.gov.tw/Datasets/Download.ashx?rid=A21030000I-D50001-001&l=https://data.nhi.gov.tw/resource/mask/maskdata.csv');
    $lines = explode(PHP_EOL, $csvData) ;
    $array = array() ;

    $user_input = $input;

    for ($i = 0; $i < count($lines) - 1; $i++) {
        $array[$i] = str_getcsv($lines[$i]);

        if ($i == 0 || ( mb_strpos($array[$i][2], $user_input, 0, "utf-8") !== false )) {
            // 去除不需要的資訊
            array_splice($array[$i], 5, 2);
            array_splice($array[$i], 3, 1);
            array_splice($array[$i], 0, 1);
        } else {
            // 去除檔案最後一行會有空白的陣列
            array_splice($array, -1, 1) ;
        }
    }

    usort($array, "cmp");
    if ($array != []) {
        $climate->table($array);
    } else {
        echo"找不到結果\n";
    }
}

while (true) {
    $input = readline('請輸入要尋找口罩的地區:');
    run($input) ;

    $input = readline("\n離開請輸入exit\n");
    if (strcmp($input, 'exit') === 0) {
        break;
    }
}
