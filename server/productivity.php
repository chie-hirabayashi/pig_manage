<?php 
// 関数読み込み
require_once __DIR__ . '/common/functions.php';

// 初期化
$bp = '';
$ep = '';
$born_num_list = [];
$pig_id_list = [];
$sum = 0;
$count = 0;
$pig_nums = 0;
$rotate = 0;
$errors = [];

// バリデーション
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bp = filter_input(INPUT_POST, 'bp');
    $ep = filter_input(INPUT_POST, 'ep');
    $errors = period_validate($bp, $ep);

    if (empty($errors)) {
        
    }
}

// 算出期間の日数
$bp_ep = strtotime($ep) - strtotime($bp);
// var_dump($bp_ep/86400);

// すべての出産情報を取得
$all_born_infos = find_all_born_infos();

// 期間内の出産情報を取得する
foreach ($all_born_infos as $all_born_info) {
    $born_day = $all_born_info['born_day'];
    if (strtotime($born_day) > strtotime($bp) && strtotime($born_day) < strtotime($ep) ) {
        
        $born_num_list[] = $all_born_info['born_num'];
        $pig_id_list[] = $all_born_info['pig_id'];
        
    }
}

$sum = array_sum($born_num_list); //合計出産頭数
$count = count($born_num_list); //合計出産回数
$pig_nums = count(array_unique($pig_id_list)); //合計個体数

if ($count != 0 || $pig_nums != 0) {
    $rotate = round(intval($count) / intval($pig_nums),2); //回転数
} 

$title = '確認menu';
?>

<!DOCTYPE html>
<html lang="ja">
<?php include_once __DIR__ . '/_head.php'; ?>
<body>
    <?php include_once __DIR__ . '/_header.php'; ?>

    <section class="productivity_content wrapper">
        <h1 class="view_title"><?= MSG_PRODUCTIVITY_MENU ?></h1>

        <?php if ($errors): ?>
            <ul class="errors">
                <?php foreach ($errors as $error): ?>
                    <li>
                        <?= h($error) ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <form class="check_form" action="" method="post">
            <h2 class="condition">▼ 確認する期間</h2>
            <div class="one_condition">
                <label class="span_label" for="bp">始期 :</label>
                <input class="span_input" type="text" name="bp" id="bp" placeholder="2020/4/1">
            </div>
            <div class="one_condition">
                <label class="span_label" for="ep">終期 :</label>
                <input class="span_input" type="text" name="ep" id="ep" placeholder="2021/3/31" >
            </div>
            <div class="button_area">
                <input type="submit" value="確 認" class="check_button"><br>
            </div>
        </form>

        <?php if (empty($errors) && $_SERVER['REQUEST_METHOD'] === 'POST'): ?>
        <div>
            <h2 class="condition">▼ <?= h($bp) ?> ~ <?= h($ep) ?>の生産性</h2>
            <table class="worikin_pig">
                <tr>
                    <th>合計産子数</th>
                    <th>合計出産回数</th>
                    <th>稼働していた母豚数</th>
                    <th>全体の実績回転数</th>
                </tr>
                <tr>
                    <td><?= h($sum) ?>頭</td>
                    <td><?= h($count) ?>回</td>
                    <td><?= h($pig_nums) ?>頭</td>
                    <td><?= h($rotate) ?>回</td>
                </tr>
            </table>
        </div>
        <?php endif; ?>
    </section>

    <?php include_once __DIR__ . '/_footer.php'; ?>
</body>
</html>
