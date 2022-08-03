<?php
// 関数読み込み
require_once __DIR__ . '/common/functions.php';

// 初期化
$indivi_num = '';
$add_day = '';
$errors = [];
$msg = '';

// バリデーション
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $indivi_num = filter_input(INPUT_POST, 'indivi_num');
    $add_day = filter_input(INPUT_POST, 'add_day');
    $errors = insert_validate($indivi_num, $add_day);

    if (empty($errors)) {
        insert_pig($indivi_num, $add_day);
    }
    $msg = MSG_INSERT_SUCCESS;
}

// $indivi_num = '99-99';
// $s = check_duplication($indivi_num);
// var_dump($s);

// $add_day = '2022-2-2';
$title = 'pig management system';
?>

<!DOCTYPE html>
<html lang="ja">
<?php include_once __DIR__ . '/_head.php'; ?>
<body>
    <?php include_once __DIR__ . '/_header.php'; ?>

    <section class="insert_content wrapper">
        <h1 class="signup_title">新規母豚登録</h1>

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
            <label class="add_label" for="add_day">導入日</label>
            <input type="text" name="add_day" id="add_day" placeholder="1999/9/9" >
            <div class="button_area">
                <input type="submit" value="新規登録" class="insert_button"><br>
                <a href="login.php" class="login_page_button">登録内容の確認はこちら</a>
            </div>
        </form>

        <?php if (empty($errors)): ?>
            <ul class="success">
                <li><?= $indivi_num . $msg ?></li>
            </ul>
        <?php endif; ?>
    </section>

    <?php include_once __DIR__ . '/_footer.php'; ?>
</body>
</html>
