<?php
// 関数読み込み
require_once __DIR__ . '/common/functions.php';

$title = 'HOME';
?>


<!DOCTYPE html>
<html lang="ja">
<?php include_once __DIR__ . '/_head.php'; ?>
<body>
    <?php include_once __DIR__ . '/_header.php'; ?>

    <section class="menu_content wrapper">
        <h1 class="index_title">MENU</h1>
        <span class="material-symbols-outlined">savings</span>
        
        <form class="" action="" method="post">
            <div class="menu_button_area">
                <a href="insert.php" class="menu_page_button1"><?= MSG_INSERT_MENU ?></a>
                <a href="insert_born_info.php" class="menu_page_button1"><?= MSG_INSERT_BORN_MENU ?></a>
                <a href="gone.php" class="menu_page_button1"><?= MSG_GONE_MENU ?></a>
                <a href="view.php" class="menu_page_button2"><?= MSG_VIEW_MENU ?></a>
                <!-- <a href="view_born_info.php" class="menu_page_button2"><?= MSG_VIEW_BORN_MENU ?></a> -->
                <a href="check.php" class="menu_page_button2"><?= MSG_CHECK_MENU ?></a>
                <a href="productivity.php" class="menu_page_button2"><?= MSG_PRODUCTIVITY_MENU ?></a>
                <a href="import.php" class="menu_page_button3"><?= MSG_IMPORT_MENU ?></a>
                <a href="" class="menu_page_button3"><?= MSG_EXPORT_MENU ?></a>
                <a href="manual.php" class="menu_page_button3"><?= MSG_MANUAL_MENU ?></a>
            </div>
        </form>

    </section>

    <?php include_once __DIR__ . '/_footer.php'; ?>
</body>
</html>

