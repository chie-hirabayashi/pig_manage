<?php

// 関数ファイルを読み込む
require_once __DIR__ . '/common/functions.php';

// データベースに接続
$dbh = connect_db();
// echo '接続しました';


// // 全データ取得
// $all_indivi_infos = find_all_indivi_infos();
// $all_born_infos = find_all_born_infos();
// // var_dump($all_born_infos);

// $period_list = [];
// $bp = '2020-1-1';
// $ep = '2021-1-1';
// $bp_ep = strtotime($ep) - strtotime($bp);
// // var_dump($bp_ep/86400);
// foreach ($all_born_infos as $all_born_info) {
//     $born_day = $all_born_info['born_day'];
//     if (strtotime($born_day) > strtotime($bp) && strtotime($born_day) < strtotime($ep) ) {
//         // echo $all_born_info['born_day'].',';
//         // リストにする
//         $born_num_list[] = $all_born_info['born_num'];
//         $pig_id_list[] = $all_born_info['pig_id'];
//         // 合計
//     }
// }
// $sum = array_sum($born_num_list);
// $count = count($born_num_list);
// // var_dump($count);// 出産回数120回
// // var_dump($sum); //合計1194匹
// // var_dump(array_unique($pig_id_list));//69匹稼動中
// // var_dump($period_list);
// // 回転数は　120/69 =1.74

// // 稼動中のデータ取得
// $working_pigs = find_working_pigs('WORKING');
// // var_dump($working_pigs[0]['indivi_num']);
// // var_dump($working_pigs[0]['add_day']);
// // 年齢算出(生後6ヶ月で導入)
// $pig_add_day = $working_pigs[0]['add_day'];
// $d_pig_add = new DATETIME($pig_add_day);
// $considered_time = new DATETIME('+6 month');
// // var_dump($pig_add_day);
// // var_dump($considered_time);
// $pig_age = $considered_time->diff($d_pig_add);
// // var_dump($pig_age->y);

// // 個体番号からpig_id取得
// $indivi_num = '61-6';
// $indivi_info = find_indivi_info($indivi_num);
// // var_dump($the_pig_info['id']);
// $pig_id = $indivi_info['id'];
// $born_info = find_born_info($pig_id);
// // var_dump($born_info);
// // $born_info[0]と$born_info[1]から直近の産子数と回転数を算出
// // var_dump($born_info[0]['born_day']);
// // var_dump($born_info[0]['born_num']);
// // var_dump($born_info[1]['born_day']);
// // var_dump($born_info[1]['born_num']);
// $born_day1 = new DateTime($born_info[0]['born_day']);
// $born_day2 = new DateTime($born_info[1]['born_day']);
// $span = $born_day1->diff($born_day2);
// // var_dump($span->days);
// $rotate = 365 / $span->days;
// // var_dump($rotate);

