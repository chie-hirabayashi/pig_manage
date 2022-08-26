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

// バリデーション
$indivi_num = filter_input(INPUT_GET, 'indivi_num');

$pig_id = get_pig_id($indivi_num);
$born_info = find_born_info($pig_id);
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
