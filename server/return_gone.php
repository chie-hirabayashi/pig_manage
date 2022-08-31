<?php  
include_once __DIR__ . '/common/functions.php';

// 初期値
$id = '';
$msg = '';

// 処理
if (isset($_GET['id']) && isset($_GET['cancel_num'])) {
    $id = filter_input(INPUT_GET, 'id');
    $cancel_num = filter_input(INPUT_GET, 'cancel_num');
    $indivi_info = find_indivi_info($id);
}

if (isset($_GET['execution']) && isset($_GET['cancel_id']) && isset($_GET['cancel_num'])) {
    $delete_id = filter_input(INPUT_GET, 'delete_id');
    $delete_num = filter_input(INPUT_GET, 'delete_num');
    delete_born_info($delete_id);
    $msg = MSG_DELETE_SUCCESS;
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
        <?php if (isset($_GET['id']) && isset($_GET['cancel_num'])): ?>
            <table>
                <tr>
                    <th>個体番号</th>
                    <th>導入日</th>
                    <th>廃用日</th>
                </tr>
                <tr>
                    <td><?= h($cancel_num) ?></td>
                    <td><?= h($indivi_info['add_day']) ?></td>
                    <td><?= h($indivi_info['left_day']) ?></td>
                </tr>
            </table>

            <form action="" class="warning-form">
                <p class=""><span class="empha"><i class="fa-solid fa-triangle-exclamation"></i>&emsp13;上記の廃用</span>&emsp13;<?= MSG_DELETE_WARNING ?></p>
                <div class="YandN_area">
                    <a href="?execution&cancel_id=<?= $id ?>&cancel_num=<?= h($cancel_num) ?>" class="yes-btn">はい</a>
                    <a href="#" onclick="history.back(-1);return false;" class="yes-btn">いいえ</a>
                </div>
            </form>
        <?php endif; ?>

        <!-- <php if (isset($_GET['execution'])): ?> -->
        <?php if (!empty($msg)): ?>
            <ul class="success">
                <li><?= h($cancel_num) ?>の廃用<?= h($msg) ?></li>
            </ul>
            <a href="edit_and_delete.php" class="manual_button4">戻&emsp;る</a>
        <?php endif; ?>
    
    </section>
</body>
</html>
