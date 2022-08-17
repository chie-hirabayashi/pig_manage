<?php 
// 関数読み込み
require_once __DIR__ . '/common/functions.php';

// 初期化
$indivi_num = '';
$rotate_condition = '';
$born_num_condition = '';
$pre_rptate_condition = '';
$errors = [];

// セッション設定
session_start();

if (isset($_SESSION)) {
    $rotate_condition = $_SESSION['rotate_condition'];
    $born_num_condition = $_SESSION['born_num_condition'];
    $pre_rptate_condition = $_SESSION['pre_rptate_condition'];
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

        <?php if (empty($errors) && isset($_SESSION)): ?>
        <div>
        <h2 class="condition">▼抽出結果</h2>
        <p>以下の条件で抽出しました</p>
        <ul class="condition_list">
            <li>実績回転数 : <?= h($rotate_condition) ?> 回以下</li>
            <li>直近2回の産子数 : 連続で<?= h($born_num_condition) ?> 頭以下</li>
            <li>予測回転数 : <?= h($pre_rptate_condition) ?> 回以下</li>
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
                <?php $flag_info = find_flag_info($pig_id) ?>

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

                <?php if ($flag_info == 1): ?>
                <?php $flag =  'flag_on' ?>
                <?php else: ?>
                <?php $flag = 'flag_off' ?>
                <?php endif; ?>

                <tr>
                    <td><?= h($extract_pig) ?><span class="<?= $flag ?>"><i class="fa-solid fa-flag"></i></span></td>
                    <td><?= h($age) ?>歳</td>
                    <td style="color: <?php echo $color1; ?>"><?= h($rotate) ?>回</td>
                    <td style="color: <?php echo $color2; ?>"><?= h($born_num_l[0]) ?>頭</td>
                    <td style="color: <?php echo $color2; ?>"><?= h($born_num_l[1]) ?>頭</td>
                    <td style="color: <?php echo $color3; ?>"><?= h($predict_rotate) ?>回</td>
                    <td><a href="check_indivi_info.php?indivi_num=<?= h($extract_pig) ?>" class="check-btn">
                        <i class="fa-solid fa-check"></i></a></td>
                </tr>
            <?php endforeach; ?>
            </table>
        </div>
        <?php endif; ?>
        <form>
            <div class="button_area">
                <a href="check.php" class="view_page_button">抽出条件の再設定はこちら</a>
            </div>
        </form>

    </section>

    <?php include_once __DIR__ . '/_footer.php'; ?>
</body>
</html>
