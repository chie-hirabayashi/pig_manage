<?php
// 関数ファイルを読み込む
require_once __DIR__ . '/common/functions.php';

// ライブラリ読込
require './vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet;


// [****.xlsx] ファイルをロードしSpreadsheetオブジェクト作成
$objSpreadsheet = IOFactory::load('./pig_db.xlsx');

$objSheet = $objSpreadsheet->getSheet(0); // 読み込むシートを指定

// ワークシート内の最大領域座標（"A1:XXXnnn" XXX:最大カラム文字列, nnn:最大行）
$strRange = $objSheet->calculateWorksheetDimension();

// ワークシートの全てのデータ取得（配列データとして）
$arrData = $objSheet->rangeToArray($strRange);

// 取得確認
// var_dump($arrData);
// echo '<pre>';
// print_r($arrData);
// echo '</pre>';

$dbh = connect_db();

foreach ($arrData as $data){
    $id = $data[0];
    $indivi_num = $data[1];
    $add_day = $data[2];
    $left_day = $data[3];
    $gone = $data[4];

    $d_add = new DateTime($add_day);

    $sql = <<<EOM
    INSERT INTO
        individual_info
        (id, indivi_num, add_day, left_day, gone)
    VALUES
        (:id, :indivi_num, :add_day, :left_day, :gone)
    EOM;

    $stmt = $dbh->prepare($sql);

    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->bindValue(':indivi_num', $indivi_num, PDO::PARAM_STR);
    $stmt->bindValue(':add_day', $d_add->format('Y-m-d'), PDO::PARAM_STR);
    if(!empty($left_day)){
        $d_left = new DateTime($left_day);
        $stmt->bindValue(':left_day', $d_left->format('Y-m-d'), PDO::PARAM_STR);
    }else{
        $stmt->bindValue(':left_day', null, PDO::PARAM_STR);
    }
    $stmt->bindValue(':gone', $gone, PDO::PARAM_INT);

    $stmt->execute();
}

function insert_individual_number($file_name)
{
// 外部ファイルを取得する関数
}
