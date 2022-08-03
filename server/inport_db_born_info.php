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

$objSheet = $objSpreadsheet->getSheet(1); // 読み込むシートを指定

// ワークシート内の最大領域座標（"A1:XXXnnn" XXX:最大カラム文字列, nnn:最大行）
$strRange = $objSheet->calculateWorksheetDimension();

// ワークシートの全てのデータ取得（配列データとして）
$arrData = $objSheet->rangeToArray($strRange);

$dbh = connect_db();

foreach ($arrData as $data){
    $id = $data[0];
    $pig_id = $data[1];
    $born_day = $data[2];
    $born_num = $data[3];

    $d_born = new DateTime($born_day);

    $sql = <<<EOM
    INSERT INTO
        born_info
        (id, pig_id, born_day, born_num)
    VALUES
        (:id, :pig_id, :born_day, :born_num)
    EOM;

    $stmt = $dbh->prepare($sql);

    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->bindValue(':pig_id', $pig_id, PDO::PARAM_STR);
    $stmt->bindValue(':born_day', $d_born->format('Y-m-d'), PDO::PARAM_STR);
    $stmt->bindValue(':born_num', $born_num, PDO::PARAM_INT);

    $stmt->execute();
}
