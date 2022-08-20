<?php
// 関数読み込み
require_once __DIR__ . '/common/functions.php';

$title = '';
?>

<!DOCTYPE html>
<html lang="ja">
<?php include_once __DIR__ . '/_head.php'; ?>
<body>
    <?php include_once __DIR__ . '/_header.php'; ?>

    <section class="manual_content wrapper">
        <h1 class="manual_title"><?= MSG_MANUAL_MENU ?></h1>
        <div>
            <p>このシステムは、出産情報を登録することで調子の悪い母豚を見つけ出すお手伝いをしてくれます。</p>
        </div>

        <div>
        <h2 class="use">▼使い方</h2>
        </div>

        <div>
            <ul class="manual_list1">
                <li class="manual_list2"><?= MSG_INSERT_MENU ?></li>
                    <p class="manual_text">新しい母豚を導入したら、個体番号と導入日を登録して下さい。</p>
                    <ul class="manual_list3">
                        <li>個体番号は、他の母豚と重複しない番号を登録して下さい。(廃用済みの母豚との重複は可能です)</li>
                        <li>導入日は過去の日付を入力して下さい。</li>
                    </ul>
                    <form class="insert_form" action="" method="post">
                        <div class="manual_button_area">
                            <a href="insert.php" class="manual_button1"><?= MSG_INSERT_MENU ?></a>
                        </div>
                    </form>
                <li class="manual_list2"><?= MSG_INSERT_BORN_MENU ?></li>
                    <p class="manual_text">母豚が出産したら、個体ごとに出産情報を登録して下さい。</p>
                    <ul class="manual_list3">
                        <li>個体番号は、稼動中の個体番号を入力して下さい。</li>
                        <li>出産日は過去の日付を入力して下さい。</li>
                        <li>出産頭数は生存数を入力して下さい。</li>
                    </ul>
                    <form class="insert_form" action="" method="post">
                        <div class="manual_button_area">
                            <a href="insert_born_info.php" class="manual_button1"><?= MSG_INSERT_BORN_MENU ?></a>
                        </div>
                    </form>
                <li class="manual_list2"><?= MSG_GONE_MENU ?></li>
                    <p class="manual_text">母豚を廃用したら、個体番号と廃用日を登録して下さい。</p>
                    <ul class="manual_list3">
                        <li>個体番号は、稼動中の個体番号を入力して下さい。</li>
                        <li>廃用日は過去の日付を入力して下さい。</li>
                    </ul>
                    <form class="insert_form" action="" method="post">
                        <div class="manual_button_area">
                            <a href="gone.php" class="manual_button1"><?= MSG_GONE_MENU ?></a>
                        </div>
                    </form>
                <li class="manual_list2"><?= MSG_VIEW_MENU ?></li>
                    <p class="manual_text">稼動中の個体番号が一覧表示されています。</p>
                    <p class="manual_text">個体番号から詳細情報を確認できます。</p>
                    <p class="manual_text">要注意の個体は、詳細情報ページでフラグをたてることができます。</p>
                    <ul class="manual_list3">
                        <li>稼動中の個体番号と個体数が現状と一致しているか定期的に確認して下さい。</li>
                        <li>現状と一致しない場合は、<?= MSG_INSERT_MENU ?>、<?= MSG_GONE_MENU ?>をして下さい。</li>
                        <li>詳細情報は、年齢、出産状況(出産日、出産頭数、回転数)を確認できます。</li>
                        <li>回転数 = 365日 / 出産間隔</li>
                    </ul>
                    <form class="insert_form" action="" method="post">
                        <div class="manual_button_area">
                            <a href="view.php" class="manual_button2"><?= MSG_VIEW_MENU ?></a>
                        </div>
                    </form>
                <li class="manual_list2"><?= MSG_CHECK_MENU ?></li>
                    <p class="manual_text">不調な母豚を抽出できます。</p>
                    <p class="manual_text">チェックアイコンから詳細情報を確認できます。</p>
                    <ul class="manual_list3">
                        <li>抽出条件は、実績回転数、直近2回の産子数、予測回転数です。</li>
                        <li>実績回転数 = 365日 / 直近2回の出産間隔</li>
                        <li>実績回転数 = 365日 / 直近2回の出産間隔</li>
                        <li>予測回転数 = 365日 / (現在 - 直近の出産日)</li>
                    </ul>
                    <form class="insert_form" action="" method="post">
                        <div class="manual_button_area">
                            <a href="check.php" class="manual_button2"><?= MSG_CHECK_MENU ?></a>
                        </div>
                    </form>
                <li class="manual_list2"><?= MSG_PRODUCTIVITY_MENU ?></li>
                    <p class="manual_text">農場全体の一定期間の生産性を確認できます。</p>
                    <ul class="manual_list3">
                        <li>確認する期間の始期、終期を入力して下さい。※確認期間は1年間を推奨します。</li>
                        <li>始期、終期は過去の日付を入力して下さい。</li>
                        <li>終期は始期より後の日付を入力して下さい。</li>
                        <li>確認できる内容は、合計産子数、合計出産回数、稼働していた母豚数、実績回転数です。</li>
                        <li>合計産子数 = 期間中に生まれた子豚の合計</li>
                        <li>合計出産回数 = 期間中に出産した回数の合計</li>
                        <li>稼働していた母豚数 = 期間中に稼働していた母豚の合計</li>
                        <li>実績回転数 = 合計出産回数 / 稼働していた母豚数 </li>
                    </ul>
                    <form class="insert_form" action="" method="post">
                        <div class="manual_button_area">
                            <a href="productivity.php" class="manual_button2"><?= MSG_PRODUCTIVITY_MENU ?></a>
                        </div>
                    </form>
                <li class="manual_list2"><?= MSG_IMPORT_MENU ?></li>
                    <p class="manual_text">初期データとしてエクセルデータを取り込むことができます。</p>
                    <ul class="manual_list3">
                        <li>説明</li>
                    </ul>
                    <form class="insert_form" action="" method="post">
                        <div class="manual_button_area">
                            <a href="import.php" class="manual_button3"><?= MSG_IMPORT_MENU ?></a>
                        </div>
                    </form>
                <li class="manual_list2">エクスポート</li>
                    <p class="manual_text">登録情報をエクセルデータで出力できます。</p>
                    <ul class="manual_list3">
                        <li>説明</li>
                    </ul>
                    <form class="insert_form" action="" method="post">
                        <div class="manual_button_area">
                            <a href="index.php" class="manual_button3">エクスポート</a>
                        </div>
                    </form>
                <li class="manual_list2">登録ミスの対処法</li>
                    <p class="manual_text">各種登録において間違って登録した場合は、以下のページから登録内容の修正または削除ができます。</p>
                    <form class="insert_form" action="" method="post">
                        <div class="manual_button_area">
                            <a href="edit_and_delete.php" class="manual_button3">登録ミスの対処法</a>
                        </div>
                    </form>
                
            </ul>
        </div>

    </section>

    <?php include_once __DIR__ . '/_footer.php'; ?>
</body>
</html>
