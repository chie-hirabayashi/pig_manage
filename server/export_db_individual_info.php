<?php
// 関数ファイルを読み込む
require_once __DIR__ . '/common/functions.php';
require './vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// DBから全データ読み込み
$all_indivi_infos = find_all_indivi_infos();
// var_dump($all_indivi_infos);

// スプレッドシート作成
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// カラム設定
$sheet->setCellValue('A1', 'ID');
$sheet->setCellValue('B1', '個体番号');
$sheet->setCellValue('C1', '導入日');
$sheet->setCellValue('D1', '廃用日');
$sheet->setCellValue('E1', '稼働状況');

// 3行目からデータを書き出す
$i = 2;
foreach ($all_indivi_infos as $all_indivi_info) {
$sheet->setCellValue('A' . $i, $all_indivi_info['id']);
$sheet->setCellValue('B' . $i, $all_indivi_info['indivi_num']);
$sheet->setCellValue('C' . $i, $all_indivi_info['add_day']);
$sheet->setCellValue('D' . $i, $all_indivi_info['left_day']);
$sheet->setCellValue('E' . $i, $all_indivi_info['gone']);
$i++;
}

// 値とセルを指定
// $sheet->setCellValue('B1', '英語');
// $sheet->setCellValue('C1', '数学');
// $sheet->setCellValue('A2', 'Aさん');
// $sheet->setCellValue('A3', 'Bさん');
// $sheet->setCellValue('B2', '90');
// $sheet->setCellValue('B3', '80');
// $sheet->setCellValue('C2', '70');
// $sheet->setCellValue('C3', '95');

// Excelファイル書き出し
$writer = new Xlsx($spreadsheet);
$writer->save('test.xlsx');
