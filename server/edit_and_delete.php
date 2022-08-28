<?php 
require_once __DIR__ . '/common/functions.php';

// 初期化
$indivi_num = '';
$check_num = '';
$choose = '';
$cancel_num = '';
$cancel_day = '';
$errors1 = [];
$errors2 = [];
$errors3 = [];
$msg = '';
$born_infos = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $indivi_num = filter_input(INPUT_POST, 'indivi_num');
    $choose = filter_input(INPUT_POST, 'edit_or_delete');
    $check_num = filter_input(INPUT_POST, 'check_num');
    $cancel_num = filter_input(INPUT_POST, 'cancel_num');
    $cancel_day = filter_input(INPUT_POST, 'cancel_day');

    if (isset($_POST['indivi_num_eandd'])) {

        $errors1 = edit_and_delete_validate($indivi_num);

        if (empty($errors1)) {
            if ($choose === 'edit') {
                header('Location: edit_indivi_num.php?indivi_num='.$indivi_num);
                exit;
            }
            if ($choose === 'delete') {
                $error = collation_born_info($indivi_num);
                if ($error === true) {
                    $msg = '出産情報が登録済みのため削除できません';
                } else {
                    $warning = MSG_DELETE_WARNING;
                }
            }
        }
    }

    if (isset($_POST['born_info_eandd'])) {
        $errors2 = edit_and_delete_validate($check_num);

        if (empty($errors2)) {
            $error = collation_born_info($check_num);
            if ($error === true) {
                $pig_id = get_pig_id($check_num);
                $born_infos = find_born_info($pig_id);
            } else {
                $msg = '出産情報が登録されていません';
            }
        }
    }

    if (isset($_POST['gone_cancel'])) {
        $errors3 = cancel_validate($cancel_num,$cancel_day);
    }
}


// 登録した個体を削除する関数
// エラーバリデーション
// 稼動中の個体を入力してください
// 削除する個体の出産情報をすべて削除してから
// function delete_indivi_num($id)
// {
//     $dbh = connect_db();

//     $sql = <<<EOM
//     DELETE
//         FROM
//             individual_info
//         WHERE
//             id = :id;
//     EOM;

//     $stmt = $dbh->prepare($sql);
//     $stmt->bindValue(':id', $id, PDO::PARAM_INT);
//     $stmt->execute();
// }
// すべての出産情報を削除する関数
function delete_born_infos($pig_id)
{
    $dbh = connect_db();

    $sql = <<<EOM
    DELETE
        FROM
            born_info
        WHERE
            pig_id = :pig_id;
    EOM;

    $stmt = $dbh->prepare($sql);
    $stmt->bindValue(':pig_id', $pig_id, PDO::PARAM_INT);
    $stmt->execute();
}



$title = '';
?>

<!DOCTYPE html>
<html lang="ja">
<?php include_once __DIR__ . '/_head.php'; ?>
<body>
    <?php include_once __DIR__ . '/_header.php'; ?>

    <section class="edit_content wrapper">
        <h1 class="edit_title">登録ミスの対処</h1>

            <h2 class="item">▼個体番号の修正・削除</h2>
                <div>
                    <p>個体番号を入力し、修正または削除を選択して下さい</p>
                    <form class="" action="" method="POST">
                        <div class="">
                            <input class="edit_and_delete_input" type="text" name="indivi_num" value="" placeholder="99-99">
                            <label for="">修正
                                <input class="radio" type="radio" name="edit_or_delete" value="edit" >
                            </label>
                            <label for="">削除
                                <input class="radio" type="radio" name="edit_or_delete" value="delete">
                            </label>
                            <input type="submit" name="indivi_num_eandd" value="選択" class="flag-btn">
                        </div>
                    </form>

                    <?php if ($errors1): ?>
                        <ul class="errors">
                            <?php foreach ($errors1 as $error1): ?>
                                <li>
                                    <?= h($error1) ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>

                    <?php if (isset($_POST['indivi_num_eandd']) && !empty($msg)): ?>
                        <ul class="errors">
                            <li><?= $msg ?></li>
                        </ul>
                    <?php endif; ?>

                    <?php if (isset($_POST['indivi_num_eandd']) && !empty($warning)): ?>
                        <form action="" class="warning-form">
                            <p class=""><span class="empha"><i class="fa-solid fa-triangle-exclamation"></i>&emsp13;<?= $indivi_num ?></span>&emsp13;<?= MSG_DELETE_WARNING ?></p>
                            <div class="YandN_area">
                                <a href="delete_indivi_num.php?indivi_num=<?= $indivi_num ?>" class="yes-btn">はい</a>
                                <a href="" class="yes-btn">いいえ</a>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>

            <h2 class="item">▼出産情報の修正・削除</h2>
                <div>
                    <p>個体番号を入力し、修正または削除する出産情報を選択して下さい</p>
                    <form class="" action="" method="POST">
                        <div class="">
                            <input class="edit_and_delete_input" type="text" name="check_num" value="" placeholder="99-99">
                            <input type="submit" name="born_info_eandd" value="選択" class="flag-btn">
                        </div>
                    </form>

                    <?php if ($errors2): ?>
                        <ul class="errors">
                            <?php foreach ($errors2 as $error2): ?>
                                <li>
                                    <?= h($error2) ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>

                    <!-- <php if (empty($errors2) && $error === true): ?> -->
                    <?php if ($born_infos): ?>
                        <p class="list_label"><?= $check_num ?>の出産情報</p>
                        <table>
                            <tr>
                                <th>出産日</th>
                                <th>産子数</th>
                                <th>修正</th>
                                <th>削除</th>
                            </tr>
                                <?php foreach($born_infos as $born_info): ?>
                            <tr>
                                <td><?= $born_info['born_day'] ?></td>
                                <td><?= $born_info['born_num'] ?></td>
                                <td><a href="edit_born_info.php?id=<?= $born_info['id'] ?>&check_num=<?= $check_num ?>" class="pencil-icon"><i class="fa-solid fa-pencil"></i></a></td>
                                <td><a href="delete_born_info.php?id=<?= $born_info['id'] ?>&check_num=<?= $check_num ?>" class="trash-icon"><i class="fa-solid fa-trash-can"></i></a></td>
                            </tr>
                                <?php endforeach; ?>
                        </table>
                    <?php endif; ?>

                    <?php if ($msg): ?>
                        <ul class="errors">
                            <li><?= $msg ?></li>
                        </ul>
                    <?php endif; ?>
                </div>

            <h2 class="item">▼廃用の取り消し</h2>
                <p>個体番号と廃用日を入力して下さい</p>
                <p>廃用日が不明な場合は取り消しできません(問い合わせ)</p>
                <form class="" action="" method="POST">
                    <div class="">
                        <input class="edit_and_delete_input" type="text" name="cancel_num" value="" placeholder="99-99">
                        <input class="edit_and_delete_input" type="date" name="cancel_date" value="">
                        
                        <input type="submit" name="gone_cancel" value="選択" class="flag-btn">
                    </div>
                </form>

                <?php if ($errors3): ?>
                    <ul class="errors">
                        <?php foreach ($errors3 as $error3): ?>
                            <li>
                                <?= h($error3) ?>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>

                <p><?= h($cancel_num) ?>の廃用を取り消しますか？</p>
                <p>バリデーション:同一番号が稼動中であれば取り消し不可能</p>
    
    </section>


</body>
</html>
