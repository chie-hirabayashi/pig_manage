<?php 
// 関数読み込み
require_once __DIR__ . '/common/functions.php';

// 初期化
$indivi_num = '';
$rotate_condition = '';
$born_num_condition = '';
$pre_rptate_condition = '';
$errors = [];

// バリデーション
// 数字で無いものを受け取ったときのエラーをつける
// 色分けのエラー解消できていない
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rotate_condition = filter_input(INPUT_POST, 'rotate_condition');
    $born_num_condition = filter_input(INPUT_POST, 'born_num_condition');
    $pre_rptate_condition = filter_input(INPUT_POST, 'pre_rptate_condition');
    $errors = check_validate($rotate_condition, $born_num_condition, $pre_rptate_condition);

    if (empty($errors)) {
        // 稼動中の個体番号を取得
        $gone = 'WORKING';
        $working_pigs = find_working_pigs($gone);
        $indivi_nums = array_column($working_pigs,'indivi_num');

        // $回転数を抽出
        $extract_pigs = [];
        foreach ($indivi_nums as $indivi_num) {
            $pig_id = get_pig_id($indivi_num);
            $rotate = get_rotate($pig_id);
            if ($rotate <= $rotate_condition) {
                $extract_pigs[] = $indivi_num;
                $change1[] = $indivi_num;
            }
        }
        // 過去２回の産子数を抽出
        foreach ($indivi_nums as $indivi_num) {
            $pig_id = get_pig_id($indivi_num);
            $born_num_l = get_born_num($pig_id);
            if ($born_num_l[0] <= $born_num_condition && $born_num_l[1] < $born_num_condition) {
                $extract_pigs[] = $indivi_num;
                $change2[] = $indivi_num;
            }
        }
        // 予測回転数を抽出
        foreach ($indivi_nums as $indivi_num) {
            $pig_id = get_pig_id($indivi_num);
            $predict_rotate = get_predict_rotate($pig_id);
            if ($predict_rotate <= $pre_rptate_condition) {
                $extract_pigs[] = $indivi_num;
                $change3[] = $indivi_num;
            }
        }
        // 配列内の重複削除
        $extract_pigs = array_unique($extract_pigs);

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
        <h1 class="view_title"><?= MSG_CHECK_MENU ?></h1>

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
            <h2 class="condition">▼ 抽出条件</h2>
            <div class="one_condition">
                <label class="condition_label" for="rotate_condition">実績回転数 :</label>
                <input class="condition_input" type="text" name="rotate_condition" id="rotate_condition" placeholder="1.8">
                <p class="condition_text">回以下 (目安:1.8)</p>
            </div>
            <div class="one_condition">
                <label class="condition_label" for="born_num_condition">産子数 :</label>
                <input class="condition_input" type="text" name="born_num_condition" id="born_num_condition" placeholder="8" >
                <p class="condition_text">頭以下 (目安:8)</p>
            </div>
            <div class="one_condition">
                <label class="condition_label" for="pre_rptate_condition">予測回転数 :</label>
                <input class="condition_input" type="text" name="pre_rptate_condition" id="pre_rptate_condition" placeholder="2.0">
                <p class="condition_text">回以下 (目安:2.0)</p>
            </div>
            <div class="button_area">
                <input type="submit" value="確 認" class="check_button"><br>
            </div>
        </form>

        <?php if (empty($errors) && $_SERVER['REQUEST_METHOD'] === 'POST'): ?>
        <div>
        <h2 class="condition">▼抽出結果</h2>
        <p>以下の条件で抽出しました</p>
        <ul class="condition_list">
            <li>実績回転数 : <?= $rotate_condition ?> 回以下</li>
            <li>直近2回の産子数 : 連続で<?= $born_num_condition ?> 頭以下</li>
            <li>予測回転数 : <?= $pre_rptate_condition ?> 回以下</li>
        </ul>
            <table class="worikin_pig">
                <tr>
                    <th>個体番号</th>
                    <th>年齢</th>
                    <th>実績回転数</th>
                    <th>産子数1</th>
                    <th>産子数2</th>
                    <th>予測回転数</th>
                    <th>詳細確認</th>
                </tr>
            <?php foreach ($extract_pigs as $extract_pig): ?>
                <?php $age = get_age($extract_pig) ?>
                <?php $pig_id = get_pig_id($extract_pig) ?>
                <?php $rotate = get_rotate($pig_id) ?>
                <?php $born_num_l = get_born_num($pig_id) ?>
                <?php $predict_rotate = get_predict_rotate($pig_id) ?>

                <?php if (!empty($change1) && in_array($extract_pig,$change1)): ?>
                <?php $color1 = 'red' ?>
                <?php else: ?>
                <?php $color1 = '#4D4D4D' ?>
                <?php endif; ?>

                <?php if (!empty($change2) && in_array($extract_pig,$change2)): ?>
                <?php $color2 = 'red' ?>
                <?php else: ?>
                <?php $color2 = '#4D4D4D' ?>
                <?php endif; ?>

                <?php if (!empty($change3) && in_array($extract_pig,$change3)): ?>
                <?php $color3 = 'red' ?>
                <?php else: ?>
                <?php $color3 = '#4D4D4D' ?>
                <?php endif; ?>

                <tr>
                    <td><?= $extract_pig?></td>
                    <td><?= $age?>歳</td>
                    <td style="color: <?php echo $color1; ?>"><?= $rotate?>回</td>
                    <td style="color: <?php echo $color2; ?>"><?= $born_num_l[0]?>頭</td>
                    <td style="color: <?php echo $color2; ?>"><?= $born_num_l[1]?>頭</td>
                    <td style="color: <?php echo $color3; ?>"><?= $predict_rotate?>回</td>
                    <td><a href="check_indivi_info.php?indivi_num=<?= h($extract_pig) ?>" class="check-btn">
                        <i class="fa-solid fa-check"></i></a></td>
                </tr>
            <?php endforeach; ?>
            </table>
        </div>
        <?php endif; ?>
        <form>
            <div class="button_area">
                <a href="view_born_info.php" class="view_page_button"><?= MSG_VIEW_BORN_MENU ?>はこちら</a>
            </div>
        </form>

    </section>

    <?php include_once __DIR__ . '/_footer.php'; ?>
</body>
</html>
