<?php
// 関数読み込み
require_once __DIR__ . '/common/functions.php';

// 初期化
$indivi_num = '';
$working_pigs = '';
$pig_age = '';
$born_info = [];
$indivi_num_array = [];
$index = '';
$the_indivi_info = [];
$rotate = 0;
$msg = '';
$errors = [];

// バリデーション
// 存在しない番号を受け取ったときのエラー設定
// view.phpで使用しているかも
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $indivi_num = filter_input(INPUT_POST, 'indivi_num');
    $errors = view_validate($indivi_num);

    if (empty($errors)) {

        $pig_id = get_pig_id($indivi_num);
        
        $born_info = find_born_info($pig_id);
        // echo '<pre>';
        // print_r($born_info);
        // echo '</pre>';

        // 年齢取得
        $the_indivi_info = find_indivi_info($pig_id);
        $pig_add_day = $the_indivi_info['add_day'];

        $d_pig_add = new DATETIME($pig_add_day);
        $considered_time = new DATETIME('+6 month');

        $pig_age = $considered_time->diff($d_pig_add);
        $pig_age = $pig_age->y;

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

        // すべての回転数算出
        $born_days = [];
        foreach ($born_info as $one_info) {
            $born_days[] = $one_info['born_day'];
        }

        $rotate_list = [];
        for ($i=0; $i < $count_info_num-1 ; $i++) { 
        $born_day1 = new DateTime($born_info[$i]['born_day']);
        $born_day2 = new DateTime($born_info[$i+1]['born_day']);
        $span = $born_day1->diff($born_day2);
        $one_rotate = round(365 / $span->days, 2);

        $rotate_list[] = $one_rotate;
        }

    }
}

$title = '確認menu';
?>

<!DOCTYPE html>
<html lang="ja">
<?php include_once __DIR__ . '/_head.php'; ?>
<body>
    <?php include_once __DIR__ . '/_header.php'; ?>

    <section class="insert_content wrapper">
        <h1 class="insert_title"><?= MSG_VIEW_BORN_MENU ?></h1>

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
            <input class="normal_input" type="text" name="indivi_num" id="indivi_num" placeholder="99-99">
            <div class="button_area">
                <input type="submit" value="確認" class="insert_button"><br>
                <!-- <a href="view.php" class="view_page_button">登録内容の確認はこちら</a> -->
            </div>
        </form>

        <?php if (empty($errors) && $_SERVER['REQUEST_METHOD'] === 'POST'): ?>
        <div class="in_content">
            <h2 class="in_title"><?= h($indivi_num) ?>の出産情報</h2>

            <ul class="indivi_info">
                <li>年齢 : <?= h($pig_age) ?> 歳</li>
                <li>直近の回転数 : <?= h($rotate) ?> 回</li>
                <li>出産状況 ▼</li>
            </ul>

            <div class="born_infos">
                <ol class="born_info1">
                <?php foreach ($born_info as $one_info): ?>
                    <li>&ensp;<?= h($one_info['born_day']) ?> : <?= h($one_info['born_num']) ?>頭</li>
                <?php endforeach; ?>
                </ol>

                <ul class="born_info2">
                <?php foreach ($rotate_list as $one_rotate): ?>
                    <li>(<?= h($one_rotate) ?> 回)</li>
                <?php endforeach; ?>
                </ul>
            </div>
        </div>
        <?php endif; ?>
    </section>

    <?php include_once __DIR__ . '/_footer.php'; ?>
</body>
</html>
