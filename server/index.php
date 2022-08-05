<?php
// 関数読み込み
require_once __DIR__ . '/common/functions.php';

$title = 'pig management system';
?>


<!DOCTYPE html>
<html lang="ja">
<?php include_once __DIR__ . '/_head.php'; ?>
<body>
    <?php include_once __DIR__ . '/_header.php'; ?>

    <section class="insert_content wrapper">
        <h1 class="insert_title">メニュー</h1>
        
        <form class="insert_form" action="" method="post">
            <div class="menu_button_area">
                <a href="insert.php" class="menu_page_button"><?= MSG_INSERT_MENU ?></a>
                <a href="insert_born_info.php" class="menu_page_button"><?= MSG_INSERT_BORN_MENU ?></a>
                <a href="gone.php" class="menu_page_button"><?= MSG_GONE_MENU ?></a>
                <a href="view.php" class="menu_page_button"><?= MSG_VIEW_MENU ?></a>
            </div>
        </form>

    </section>

    <?php include_once __DIR__ . '/_footer.php'; ?>
</body>
</html>

