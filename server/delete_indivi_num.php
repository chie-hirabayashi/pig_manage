<?php  
include_once __DIR__ . '/common/functions.php';

// 初期化
$indivi_num = '';

// 処理
if (isset($_GET['indivi_num'])) {
    $indivi_num = filter_input(INPUT_GET, 'indivi_num');

    $pig_id = get_pig_id($indivi_num);
    delete_indivi_num($pig_id);
    $msg = MSG_DELETE_SUCCESS;
}

$title = ''
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <?php include_once __DIR__ . '/_head.php' ?>
</head>
<body>
    <?php include_once __DIR__ . '/_header.php' ?>
    
    <section class="edit_content wrapper">
        <ul class="success">
            <li><?= $indivi_num . MSG_DELETE_SUCCESS ?></li>
        </ul>
        <a href="edit_and_delete.php" class="manual_button4">戻る</a>
    </section>
</body>
</html>
