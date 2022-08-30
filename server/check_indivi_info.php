<?php
// 関数読み込み
require_once __DIR__ . '/common/functions.php';

// 初期化
$born_info = [];
$errors = [];
$indivi_num = '';
$indivi_num_array = [];
$index = '';
$msg = '';
$pig_age = '';
$rotate = 0;
$the_indivi_info = [];
$working_pigs = '';
$status = 0;
$born_days = [];
$born_nums = [];

// バリデーション
$indivi_num = filter_input(INPUT_GET, 'indivi_num');

$pig_id = get_pig_id($indivi_num);
$born_infos_ASC = get_born_infos_ASC($pig_id);
$born_infos_DESC = get_born_infos_DESC($pig_id);
$status = find_flag_info($pig_id);

// 年齢を取得
$pig_age = pig_age($pig_id);

// 回転数算出(直近の回転数を算出)
$rotate = view_rotate($pig_id);

// 1個体すべての回転数を算出
$rotate_list = get_rotate_l($pig_id);

// フラグ識別
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $status = filter_input(INPUT_POST, 'watch');
    flag($pig_id, $status);
}

// グラフの準備
foreach ($born_infos_ASC as $one_born_info) {
    $born_days[] = $one_born_info['born_day'];
    $born_nums[] = $one_born_info['born_num'];
}

$rotate_null = [null];
$rotates = array_merge($rotate_null,$rotate_list);

// グラフの値を準備
$x = $born_days;
$y_num = $born_nums;
$y_rotate = $rotates;
//javascriptに渡す
$jx = json_encode($x);
$jy_n = json_encode($y_num);
$jy_r = json_encode($y_rotate);

$title = '確認nemu';
?>

<!DOCTYPE html>
<html lang="ja">
<?php include_once __DIR__ . '/_head.php'; ?>
<body>
    <?php include_once __DIR__ . '/_header.php'; ?>

    <section class="born_info_content wrapper">
        <?php include_once __DIR__ . '/_indivi_info.php'; ?>
            <!-- <a href="#" onclick="history.back(-1);return false;" class="manual_button2">戻&emsp;る</a> -->
            <!-- <a href="javascript:history.back()" class="manual_button2">戻&emsp;る</a> -->
            <a href="check_result.php" class="manual_button4">戻&emsp;る</a>
    </section>

    <?php include_once __DIR__ . '/_footer.php'; ?>
</body>
</html>
