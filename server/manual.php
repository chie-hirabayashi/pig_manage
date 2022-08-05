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

    <section class="manual_content wrapper">
        <h1 class="insert_title"><?= MSG_MANUAL_MENU ?></h1>
        <div>
            <p>このシステムは・・・</p>
        
        </div>

        <form class="insert_form" action="" method="post">
            <div class="button_area">
                <a href="view.php" class="view_page_button"><?= MSG_VIEW_MENU ?>はこちら</a>
            </div>
        </form>
    </section>

    <?php include_once __DIR__ . '/_footer.php'; ?>
</body>
</html>
