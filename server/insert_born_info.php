<?php
// 関数読み込み
require_once __DIR__ . '/common/functions.php';

// 初期化
$indivi_num = '';
$born_day = '';
$born_num = '';
$pig_id = '';
$errors = [];
$msg = '';

// バリデーション
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $indivi_num = filter_input(INPUT_POST, 'indivi_num');
    $born_day = filter_input(INPUT_POST, 'born_day');
    $born_num = filter_input(INPUT_POST, 'born_num');
    $errors = insert_born_validate($indivi_num, $born_day, $born_num);

    if (empty($errors)) {
        // 稼動中の個体データ取得
        $working_pigs = find_working_pigs('WORKING');
        // 稼動中の個体データからpig_id取得
        foreach ($working_pigs as $working_pig) {
            if ($working_pig['indivi_num'] == $indivi_num) {
                $pig_id = $working_pig['id'];
            } 
        }
        if (!empty($pig_id)) {
            // 出産情報登録
            insert_born_info($pig_id, $born_day, $born_num);
        } else {
            $errors[] = MSG_DONT_WORKING;
            $errors[] = MSG_INFO;
        }
    }
    $msg = MSG_INSERT_SUCCESS;
}
// var_dump($working_pigs);
// var_dump($pig_id);

$title = '登録menu';
?>

<!DOCTYPE html>
<html lang="ja">
<?php include_once __DIR__ . '/_head.php'; ?>
<body>
    <?php include_once __DIR__ . '/_header.php'; ?>

    <section class="insert_content wrapper">
        <h1 class="insert_title"><?= MSG_INSERT_BORN_MENU ?></h1>

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
            <label class="born_day_label" for="born_day">出産日</label>
            <input class="normal_input" type="date" name="born_day" id="born_day" placeholder="1999/9/9" >
            <label class="born_num_label" for="born_num">出産頭数</label>
            <input class="normal_input" type="text" name="born_num" id="born_num" placeholder="9" >
            <div class="button_area">
                <input type="submit" value="出産情報登録" class="insert_button"><br>
                <a href="view.php" class="view_page_button"><?= MSG_VIEW_MENU ?>はこちら</a>
                <!-- <a href="" class="view_page_button">登&emsp;録</a> -->
            </div>
        </form>

        <?php if (empty($errors)): ?>
            <ul class="success">
                <li><?= h($indivi_num) . $msg ?></li>
            </ul>
        <?php endif; ?>
    </section>

    <?php include_once __DIR__ . '/_footer.php'; ?>
</body>
</html>
