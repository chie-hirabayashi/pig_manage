<?php  
include_once __DIR__ . '/common/functions.php';

// 初期値
$id = '';
$msg = '';
$delete_num = '';
$delete_id = '';

// 処理
if (isset($_GET['id']) && isset($_GET['check_num'])) {
    $id = filter_input(INPUT_GET, 'id');
    $check_num = filter_input(INPUT_GET, 'check_num');
    $born_info = get_born_info($id);
}

if (isset($_GET['execution']) && isset($_GET['delete_id']) && isset($_GET['delete_num'])) {
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
        <?php if (isset($_GET['id']) && isset($_GET['check_num'])): ?>
            <form action="" class="warning-form">
                <p class=""><span class="empha"><i class="fa-solid fa-triangle-exclamation"></i>&emsp13;以下の出産情報</span>&emsp13;<?= MSG_DELETE_WARNING ?></p>
                <div class="YandN_area">
                    <a href="?execution&delete_id=<?= $id ?>&delete_num=<?= $check_num ?>" class="yes-btn">はい</a>
                    <a href="#" onclick="history.back(-1);return false;" class="yes-btn">いいえ</a>
                </div>
            </form>

            <table>
                <tr>
                    <th>個体番号</th>
                    <th>出産日</th>
                    <th>産子数</th>
                </tr>
                <tr>
                    <td><?= $check_num ?></td>
                    <td><?= $born_info['born_day'] ?></td>
                    <td><?= $born_info['born_num'] ?></td>
                </tr>
            </table>
        <?php endif; ?>

        <?php if (isset($_GET['execution'])): ?>
            <ul class="success">
                <li><?= $delete_num ?>の出産情報を<?= $msg ?></li>
            </ul>
            <a href="edit_and_delete.php" class="manual_button4">戻&emsp;る</a>
        <?php endif; ?>
    
    </section>
</body>
</html>
