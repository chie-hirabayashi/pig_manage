<?php
// 関数読み込み
require_once __DIR__ . '/common/functions.php';

// 初期化
$indivi_num = '';
$working_pigs = '';
$indivi_num_array = [];
$index = '';
$the_indivi_info = [];
$rotate = 0;
$msg = '';
$errors = [];

// $indivi_num = '4-28';
// $indivi_num = '41-32';
// $indivi_num = '61-6';
// $indivi_num = '99-99';
// $indivi_num = '88-00';

//バリデーション正常(POSTしていない状態で)
// $c = check_gone($indivi_num);
// var_dump($c);
// $errors = view_validate($indivi_num);
// var_dump($errors);

// バリデーション
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $indivi_num = filter_input(INPUT_POST, 'indivi_num');
    $errors = view_validate($indivi_num);

    if (empty($errors)) {

        $working_pigs = find_working_pigs('WORKING'); //稼動中のデータ取得
        // indivi_numを配列化して、インデックスを取り出す
        $indivi_num_array = array_column($working_pigs, 'indivi_num');
        $index = array_search($indivi_num, $indivi_num_array);
        // var_dump($index);
        // var_dump($indivi_num);
        // var_dump($indivi_num_array);

        // 稼動中のデータからview対象の配列を取り出す
        $the_indivi_info = $working_pigs[$index];

        // pig_id取得
        $pig_id = $the_indivi_info['id'];
        $born_info = find_born_info($pig_id);
        // echo '<pre>';
        // print_r($born_info);
        // echo '</pre>';

        // 年齢取得
        $pig_add_day = $the_indivi_info['add_day'];

        $d_pig_add = new DATETIME($pig_add_day);
        $considered_time = new DATETIME('+6 month');

        $pig_age = $considered_time->diff($d_pig_add);
        // var_dump($pig_age->y);

        // 回転数算出(直近の回転数を算出)
        $count_info_num = count($born_info);
        if ($count_info_num == 0) {
            $rotate = 0;
        } elseif ($count_info_num == 1) {
            $rotate = 1;
        } else {
            $born_day1 = new DateTime($born_info[0]['born_day']);
            $born_day2 = new DateTime($born_info[1]['born_day']);
            $span = $born_day1->diff($born_day2);
            $rotate = round(365 / $span->days, 2);
        }
    }
}
// var_dump($indivi_num_array);
// var_dump($indivi_num);
// var_dump($errors);

// }
$title = 'pig management system';
?>

<!DOCTYPE html>
<html lang="ja">
<?php include_once __DIR__ . '/_head.php'; ?>
<body>
    <?php include_once __DIR__ . '/_header.php'; ?>

    <section class="insert_content wrapper">
        <h1 class="insert_title">母豚の状態確認</h1>

        <?php if ($errors): ?>
            <ul class="errors">
                <?php foreach ($errors as $error): ?>
                    <li>
                        <?= h($error) ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
        
        <form class="insert_form" action="" method="post">
            <label class="indivi_label" for="indeivi_num">個体番号</label>
            <input type="text" name="indivi_num" id="indivi_num" placeholder="99-99">
            <div class="button_area">
                <input type="submit" value="確認" class="insert_button"><br>
                <!-- <a href="view.php" class="view_page_button">登録内容の確認はこちら</a> -->
            </div>
        </form>

        <?php if (empty($errors)): ?>
        <div class="in_content">
            <h2 class="in_title"><?= h($indivi_num) ?>の状態</h2>
            <ul class="indivi_info">
                <li>年齢 : <?= h($pig_age->y) ?> 歳</li>
                <li>直近の回転数 : <?= h($rotate) ?> 回</li>
                <li>出産状況 ▼</li>
            </ul>

            <ol class="born_info">
            <?php foreach ($born_info as $one_info): ?>
                <li>&ensp;<?= h($one_info['born_day']) ?> : <?= h($one_info['born_num']) ?>頭</li>
            <?php endforeach; ?>
            </ol>
        </div>
        <?php endif; ?>
    </section>

    <?php include_once __DIR__ . '/_footer.php'; ?>
</body>
</html>
