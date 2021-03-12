<?php

/**
 * 這個函式只是作為usort排序的條件
 * @ param 二維陣列 $a  $a[2]存的是成人口罩數量
 * @ param 二維陣列 $b  $b[2]存的是成人口罩數量
 * @ return 0 or 1 or -1 決定如何排序
 */

function cmp($a,$b)
{
    /** 如果$a[2]是中文標題,回傳-1(不被往下交換) */
    if ( mb_strlen($a[2], "UTF-8") != strlen( ($a[2]) ) ) {
        return -1 ;
    }

    /** 如果$b[2]是中文標題,回傳1(往上交換) */
    if ( mb_strlen($b[2], "UTF-8") != strlen( ($b[2]) ) ) {
        return 1 ;
    }

    /** 如果$a與$b數字相同就不交換 */
    if ( (int)$a[2] == (int)$b[2] ) {
        return 0 ;
    }

    return( (int)$a[2] > (int)$b[2] ) ? -1 : 1 ;
}

/**
 * 這個函式負責程式的主要執行
 * @ param string $input 使用者輸入的參數(地名)
 * @ return void
 */

function run( $input ) {

    /** 引入了composer裡的CLImate作為輸出的排版使用 */
    require_once('vendor/autoload.php') ;
    $climate = new League\CLImate\CLImate;

    /** 讀取衛福部提供剩餘口罩的CSV檔案網址,用二維陣列儲存並做處理資訊的操作 */
    $csvData = file_get_contents('https://data.nhi.gov.tw/Datasets/Download.ashx?rid=A21030000I-D50001-001&l=https://data.nhi.gov.tw/resource/mask/maskdata.csv');

    /** @變數 array $lines 將CSV檔案裡的以行存入陣列 */
    $lines = explode(PHP_EOL, $csvData) ;

    /** @變數 array $array 存需要資訊的陣列 */
    $array = array() ;

    $user_input = $input;

    for( $i = 0 ; $i < count($lines)-1; $i++ ) {

        /** $lines 是CSV裡的一行,以逗點做分割存入$array */
        $array[$i] = str_getcsv($lines[$i]);

        if ( $i == 0 || ( mb_strpos( $array[$i][2], $user_input,0,"utf-8" ) !== false ) ){
            /** 去除不需要的資訊(ex:兒童口罩,電話,編號) */ 
            array_splice($array[$i],5,2); 
            array_splice($array[$i],3,1);
            array_splice($array[$i],0,1);
        } 
        else {
            /** 去除csv檔裡最後一行會有空白 */
            array_splice($array,-1,1) ;
        } 


    }
    
    usort($array,"cmp");

    if ( $array != [] ) {       
        $climate->table($array);
    }
    else{
        echo"找不到結果\n";
    }
    
}

/** 程式從這裡進入 */

while ( true ) {

    $input = readline('請輸入要尋找口罩的地區:');
    run($input) ;

    $input = readline("\n離開請輸入exit\n");
    if ( strcmp($input,'exit') === 0 ) {
        break;
    }
}