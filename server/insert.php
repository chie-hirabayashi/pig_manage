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

$title = '登録menu';
?>

<!DOCTYPE html>
<html lang="ja">
<?php include_once __DIR__ . '/_head.php'; ?>
<body>
    <?php include_once __DIR__ . '/_header.php'; ?>

    <section class="insert_content wrapper">
        <h1 class="insert_title"><?= MSG_INSERT_MENU ?></h1>

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
            <label class="add_label" for="add_day">導入日</label>
            <input class="normal_input" type="date" name="add_day" id="add_day" placeholder="1999/9/9" >
            <div class="button_area">
                <input type="submit" value="新規登録" class="insert_button"><br>
                <a href="view.php" class="view_page_button"><?= MSG_VIEW_MENU ?>はこちら</a>
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
