<?php
// 関数読み込み
require_once __DIR__ . '/common/functions.php';

// 検索機能をつける(調べたい個体が稼働中か確認するため)
// 検索番号:99-99
// 結果:99-99は稼動中です、廃用済みです、登録されていません

// 初期化
$errors = [];
$working_pigs = [];

// 稼動中のデータ取得
$working_pigs = find_working_pigs('WORKING');
$working_pigs_count = count($working_pigs);
// var_dump($working_pigs);
foreach ($working_pigs as $working_pig) {
    $w_pig_num_l[] = $working_pig['indivi_num'];
    $w_pig_add_l[] = $working_pig['add_day'];
}
// var_dump($working_pig_list);
$w_pig_num_l_chunk = array_chunk($w_pig_num_l,10);
$w_pig_add_l_chunk = array_chunk($w_pig_add_l,10);


// var_dump($working_pigs[0]['add_day']);
// 年齢算出(生後6ヶ月で導入)
$pig_add_day = $working_pigs[0]['add_day'];
$d_pig_add = new DATETIME($pig_add_day);
$considered_time = new DATETIME('+6 month');
// var_dump($pig_add_day);
// var_dump($considered_time);
$pig_age = $considered_time->diff($d_pig_add);
// var_dump($pig_age->y);

$title = 'pig management system';
?>

<!DOCTYPE html>
<html lang="ja">
<?php include_once __DIR__ . '/_head.php'; ?>
<body>
    <?php include_once __DIR__ . '/_header.php'; ?>

    <section class="view_content wrapper">
        <h1 class="view_title"><?= MSG_VIEW_MENU ?></h1>

        <?php if ($errors): ?>
            <ul class="errors">
                <?php foreach ($errors as $error): ?>
                    <li>
                        <?= h($error) ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
        
        <div>
            <table class="worikin_pig">
            <?php foreach ($w_pig_num_l_chunk as $w_pig_num): ?>
                <tr>
                <?php foreach($w_pig_num as $w_indivi_num): ?>
                    <td><?= $w_indivi_num?></td>
                <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
            </table>
            <p>現在、<span class="emphasize"><?=h($working_pigs_count)?>頭</span>が稼動中です</p>
        </div>
        <form>
            <div class="button_area">
                <a href="insert.php" class="view_page_button"><?= MSG_INSERT_MENU ?>はこちら</a>
                <a href="gone.php" class="view_page_button"><?= MSG_GONE_MENU ?>はこちら</a>
            </div>
        </form>

    </section>

    <?php include_once __DIR__ . '/_footer.php'; ?>
</body>
</html>
