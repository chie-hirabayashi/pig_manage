<?php
// 関数読み込み
require_once __DIR__ . '/common/functions.php';

// 検索機能をつける(調べたい個体が稼働中か確認するため)
// 検索番号:99-99
// 結果:99-99は稼動中です、廃用済みです、登録されていません

// 初期化
$errors = [];
$working_pigs = [];
$status = 0;

// 稼動中のデータ取得
$working_pigs = find_working_pigs('WORKING');
$working_pigs_count = count($working_pigs);
// var_dump($working_pigs);
foreach ($working_pigs as $working_pig) {
    $w_pig_num_l[] = $working_pig['indivi_num'];
    $w_pig_add_l[] = $working_pig['add_day'];
    $w_pig_flag_l[] = $working_pig['flag'];
    $w_pig_l[] = array('indivi_num'=>$working_pig['indivi_num'],'add_day'=>$working_pig['add_day'],'flag'=>$working_pig['flag']);
}

// $w_pig_num_l_chunk = array_chunk($w_pig_num_l,10);
// $w_pig_add_l_chunk = array_chunk($w_pig_add_l,10);
$w_pig_l_chunk = array_chunk($w_pig_l,5);
// var_dump($w_pig_l_chunk);


// var_dump($working_pigs[0]['add_day']);
// 年齢算出(生後6ヶ月で導入)
$pig_add_day = $working_pigs[0]['add_day'];
$d_pig_add = new DATETIME($pig_add_day);
$considered_time = new DATETIME('+6 month');
// var_dump($pig_add_day);
// var_dump($considered_time);
$pig_age = $considered_time->diff($d_pig_add);
// var_dump($pig_age->y);

foreach ($w_pig_flag_l as $w_pig_flag ) {
    if ($status == 1){
        $flag =  'flag_on';
    }else{
        $flag = 'flag_off';
    }
}

$title = '確認menu';
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
            <table>
            <!-- <php foreach ($w_pig_num_l_chunk as $w_pig_num): ?> -->
                <tr>
                    <th>個体番号<br>(導入日)</th>
                    <th>個体番号<br>(導入日)</th>
                    <th>個体番号<br>(導入日)</th>
                    <th>個体番号<br>(導入日)</th>
                    <th>個体番号<br>(導入日)</th>
                </tr>
            <?php foreach ($w_pig_l_chunk as $w_pig_l): ?>
                <tr>
                <?php foreach($w_pig_l as $one_w_pig_l): ?>
                    
                    <?php if ($one_w_pig_l['flag'] == 1): ?>
                    <?php $flag =  'flag_on' ?>
                    <?php else: ?>
                    <?php $flag = 'flag_off' ?>
                    <?php endif; ?>

                    <td><a href="check_indivi_info.php?indivi_num=<?= h($one_w_pig_l['indivi_num']) ?>"><?= $one_w_pig_l['indivi_num'] ?></a>
                        <span class="<?= $flag ?>"><i class="fa-solid fa-flag"></i></span>
                        <br>(<?= $one_w_pig_l['add_day'] ?>)</td>
                <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
            </table>
            <p>現在、<span class="emphasize"><?=h($working_pigs_count)?>頭</span>が稼動中です</p>
        </div>
        <form>
            <div class="button_area">
                <a href="insert.php" class="insert_page_button"><?= MSG_INSERT_MENU ?>はこちら</a>
                <a href="insert_born_info.php" class="insert_page_button"><?= MSG_INSERT_BORN_MENU ?>はこちら</a>
                <a href="gone.php" class="insert_page_button"><?= MSG_GONE_MENU ?>はこちら</a>
            </div>
        </form>

    </section>

    <?php include_once __DIR__ . '/_footer.php'; ?>
</body>
</html>
