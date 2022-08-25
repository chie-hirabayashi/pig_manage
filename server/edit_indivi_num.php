<?php  
include_once __DIR__ . '/common/functions.php';

$id = 0;
$before_indivi_num = '';
$after_indivi_num = '';
$indivi_num = '';
$errors = [];


//この書き方もあるけどnoticeがでないようにfilter_input
// $indivi_num = $_GET['indivi_num']; 
$indivi_num = filter_input(INPUT_GET, 'indivi_num');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $before_indivi_num = filter_input(INPUT_POST, 'before_indivi_num');
    $after_indivi_num = filter_input(INPUT_POST, 'after_indivi_num');
    $errors = edit_validate($after_indivi_num,$before_indivi_num);
    // $errors = edit_and_delete_validate($before_indivi_num);


    if (empty($errors)) {
        $pig_id = get_pig_id($before_indivi_num);
        edit_indivi_num($pig_id, $after_indivi_num);
    }


    // header('Location: index.php');
    $msg = MSG_EDIT_SUCCESS;
    $indivi_num = '';
}


$title = '個体番号の修正'
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <?php require_once __DIR__ . '/_head.php' ?>
</head>
<body>
    <?php require_once __DIR__ . '/_header.php' ?>

    <section class="edit_content wrapper">
        <h1 class="edit_title">個体番号の修正</h1>

            <?php if ($errors): ?>
                <ul class="errors">
                    <?php foreach ($errors as $error): ?>
                        <li>
                            <?= h($error) ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>

            <form class="" action="" method="POST">
                <!-- <div class="before_area">
                <label class="before_label">修正前の個体番号</label>
                <text class="before_indivi_num"><= h($indivi_num) ?></text>
                </div> -->
                <label for="">修正前の個体番号</label>
                <input class="edit_and_delete_input" type="text" name="before_indivi_num" value=<?= h($indivi_num) ?>><br>
                <label for="">修正後の個体番号</label>
                <input class="edit_and_delete_input" type="text" name="after_indivi_num" value=""><br>
                <input type="submit" value="修正" class="flag-btn">
            </form>
            
            <?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($errors)): ?>
                <ul class="success">
                    <li><?= h($before_indivi_num) . 'から' . h($after_indivi_num) . $msg ?></li>
                </ul>
            <?php endif; ?>
    </section>

</body>
</html>
