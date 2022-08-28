<?php  
include_once __DIR__ . '/common/functions.php';

// 初期値
$id = '';
$msg = '';
$delete_num = '';
$delete_id = '';
$new_born_day = '';
$new_born_num = 0;

// 処理
if (isset($_GET['id']) && isset($_GET['check_num'])) {
    $id = filter_input(INPUT_GET, 'id');
    $check_num = filter_input(INPUT_GET, 'check_num');
    $born_info = get_born_info($id);
}

// 表示をif文で分岐するために$_GETを使用($_SERVERでは分岐できない)
if (isset($_GET['new_born_day']) && isset($_GET['new_born_num']) && isset($_GET['id'])) {
// if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_born_day = filter_input(INPUT_GET, 'new_born_day');
    $new_born_num = filter_input(INPUT_GET, 'new_born_num');
    $id = filter_input(INPUT_GET, 'id');
    edit_born_info($id, $new_born_day, $new_born_num);
    $msg = MSG_EDIT_SUCCESS;
}
var_dump($new_born_day);
var_dump($new_born_num);
var_dump($id);



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
        <?php if (isset($_GET['id']) && isset($_GET['check_num'])): ?>
        <!-- <php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?> -->
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
                <tr>
                    <td>修正後</td>
                    <td style="width: 25%;"><?= $check_num ?></td>
                <form action="" method="">
                    <td style="width: 25%;"><input type="text" class="table_input" name="new_born_day"></td>
                    <td style="width: 25%;"><input type="text" class="table_input" name="new_born_num"></td>
                </tr>
            </table>
                    <input type="hidden" name="id" value="<?= $id ?>">
                    <input type="submit" value="修&emsp;正" class="eandd_button">
                </form>
        <?php endif; ?>

        <!-- <php if (isset($_GET['new_born_day']) && isset($_GET['new_born_num'])): ?> -->
        <?php if (!empty($msg)): ?>
            <ul class="success">
                <li>出産情報<?= $msg ?></li>
            </ul>
        <?php endif; ?>
            <a href="edit_and_delete.php" class="manual_button4">戻&emsp;る</a>
            <a href="view.php" class="view_page_button"><?= MSG_VIEW_MENU ?>はこちら</a>
    
    </section>
</body>
</html>
