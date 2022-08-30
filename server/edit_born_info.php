<?php  
include_once __DIR__ . '/common/functions.php';

// 初期値
$id = '';
$msg = '';
$indivi_num = '';
$new_born_day = '';
$new_born_num = 0;
$errors = [];

// 処理
if (isset($_GET['id']) && isset($_GET['check_num'])) {
    $id = filter_input(INPUT_GET, 'id');
    $check_num = filter_input(INPUT_GET, 'check_num');
    $born_info = get_born_info($id);
}

// 表示をif文で分岐するために$_GETを使用($_SERVERでは分岐できない)
// if (isset($_GET['new_born_day']) && isset($_GET['new_born_num']) && isset($_GET['id'])) {
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_born_day = filter_input(INPUT_POST, 'new_born_day');
    $new_born_num = filter_input(INPUT_POST, 'new_born_num');
    // $id = filter_input(INPUT_POST, 'id');
    // $check_num = filter_input(INPUT_POST, 'check_num');
    $errors = insert_born_validate($check_num, $new_born_day, $new_born_num);

    if (empty($errors)) {
        edit_born_info($id, $new_born_day, $new_born_num);
        $msg = MSG_EDIT_SUCCESS;
    }

}

$title = ''
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <?php include_once __DIR__ . '/_head.php'; ?>
</head>
<body>
    <?php include_once __DIR__ . '/_header.php'; ?>

    <section class="edit_content wrapper">
        <?php if ($errors): ?>
            <ul class="errors">
                <?php foreach ($errors as $error): ?>
                    <li>
                        <?= h($error) ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <?php if (empty($errors) && !empty($msg)): ?>
            <ul class="success">
                <li><?= $check_num ?>の出産情報<?= $msg ?></li>
            </ul>
        <?php endif; ?>

        <table>
            <tr>
                <th></th>
                <th>個体番号</th>
                <th>出産日</th>
                <th>産子数</th>
            </tr>
            <tr>
                <td>修正前</td>
                <td><?= $check_num ?></td>
                <td><?= $born_info['born_day'] ?></td>
                <td><?= $born_info['born_num'] ?></td>
            </tr>
        <form action="" method="POST">
            <tr>
                <td>修正後</td>
                <td style="width: 25%;"><?= $check_num ?></td>
                <td style="width: 25%;"><input type="date" class="table_input" name="new_born_day" value="<?= $new_born_day ?>"></td>
                <td style="width: 25%;"><input type="number" min="1" max="20" class="table_input" name="new_born_num" value="<?= $new_born_num ?>"></td>
            </tr>
        </table>
            <input type="submit" value="修&emsp;正" class="eandd_button">
        </form>

            <a href="edit_and_delete.php" class="manual_button4">戻&emsp;る</a>
            <a href="view.php" class="view_page_button"><?= MSG_VIEW_MENU ?>はこちら</a>
    </section>
</body>
</html>
