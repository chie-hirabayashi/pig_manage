<?php 
require_once __DIR__ . '/common/functions.php';

// 初期化
$indivi_num = '';
$check_num = '';
$choose = '';
$cancel_num = '';
$cancel_day = '';
$IndiviErrors = [];
$BornErrors = [];
$GoneErrors = [];
$IndiviErrMsg = '';
$BornErrMsg = '';
$GoneErrMsg = '';
$SuccessMsg = '';
$born_infos = [];
$gone_pigs = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $indivi_num = filter_input(INPUT_POST, 'indivi_num');
    $choose = filter_input(INPUT_POST, 'edit_or_delete');
    $check_num = filter_input(INPUT_POST, 'check_num');
    $cancel_num = filter_input(INPUT_POST, 'cancel_num');
    $cancel_day = filter_input(INPUT_POST, 'cancel_day');

    $id = filter_input(INPUT_POST, 'id');
    $cancel_num = filter_input(INPUT_POST, 'cancel_num');

    if (isset($_POST['indivi_num_eandd'])) {
        $IndiviErrors = edit_and_delete_validate($indivi_num);
        if (empty($IndiviErrors)) {
            if ($choose === 'edit') {
                header('Location: edit_indivi_num.php?indivi_num='.$indivi_num);
                exit;
            }
            if ($choose === 'delete') {
                if (collation_born_info($indivi_num)) {
                    $IndiviErrMsg = '出産情報が登録済みのため削除できません';
                } else {
                    $warning = MSG_DELETE_WARNING;
                }
            }
        }
    }

    if (isset($_POST['born_info_eandd'])) {
        $BornErrors = edit_and_delete_validate($check_num);
        if (empty($BornErrors)) {
            if (collation_born_info($check_num)) {
                $pig_id = get_pig_id($check_num);
                $born_infos = get_born_infos_ASC($pig_id);
            } else {
                $BornErrMsg = '出産情報が登録されていません';
            }
        }
    }

    if (isset($_POST['gone_cancel'])) {
        $GoneErrors = cancel_validate($cancel_num);
        if (empty($GoneErrors)) {
            $gone_pigs = get_gone_pigs($cancel_num);
        }
    }

    if ($id) {
        return_gone($id);
        $SuccessMsg = MSG_RETURN_SUCCESS;
    }
}

var_dump($id);
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
            <?php if ($IndiviErrors): ?>
                <ul class="errors">
                    <?php foreach ($IndiviErrors as $IndiviError): ?>
                        <li>
                            <?= h($IndiviError) ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
            <?php if (!empty($IndiviErrMsg)): ?>
                <ul class="errors">
                    <li><?= h($IndiviErrMsg) ?></li>
                </ul>
            <?php endif; ?>
            <?php if (!empty($warning)): ?>
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
            <?php if ($BornErrors): ?>
                <ul class="errors">
                    <?php foreach ($BornErrors as $BornError): ?>
                        <li>
                            <?= h($BornError) ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
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
                        <td><?= h($born_info['born_day']) ?></td>
                        <td><?= h($born_info['born_num']) ?></td>
                        <td><a href="edit_born_info.php?id=<?= h($born_info['id']) ?>&check_num=<?= h($check_num) ?>" class="pencil-icon"><i class="fa-solid fa-pencil"></i></a></td>
                        <td><a href="delete_born_info.php?id=<?= h($born_info['id']) ?>&check_num=<?= h($check_num) ?>" class="trash-icon"><i class="fa-solid fa-trash-can"></i></a></td>
                    </tr>
                        <?php endforeach; ?>
                </table>
            <?php endif; ?>
            <?php if ($BornErrMsg): ?>
                <ul class="errors">
                    <li><?= h($BornErrMsg) ?></li>
                </ul>
            <?php endif; ?>
        </div>
        <h2 class="item">▼廃用の取り消し</h2>
        <div>
            <p>個体番号を入力し、廃用を取り消して下さい</p>
            <form class="" action="" method="POST">
                <div class="">
                    <input class="edit_and_delete_input" type="text" name="cancel_num" value="" placeholder="99-99">
                    <!-- <input class="edit_and_delete_input" type="date" name="cancel_date" value=""> -->
                    
                    <input type="submit" name="gone_cancel" value="選択" class="flag-btn">
                </div>
            </form>
            <?php if ($GoneErrors): ?>
                <ul class="errors">
                    <?php foreach ($GoneErrors as $GoneError): ?>
                        <li>
                            <?= h($GoneError) ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
            <?php if ($gone_pigs): ?>
                <p class="list_label"><?= $cancel_num ?>の廃用情報</p>
                <table>
                    <tr>
                        <th>導入日</th>
                        <th>廃用日</th>
                        <th>取消</th>
                    </tr>
                        <!-- aタグでPOSTする(GETだと他の動作に影響する恐れがあるため) -->
                        <form action="" method="POST" name="form1">
                        <?php foreach($gone_pigs as $gone_pig): ?>
                    <tr>
                        <td><?= h($gone_pig['add_day']) ?></td>
                        <td><?= h($gone_pig['left_day']) ?></td>
                        <td>
                            <input type="hidden" name="id" value="<?= h($gone_pig['id']) ?>">
                            <input type="hidden" name="cancel_num" value="<?= h($cancel_num) ?>">
                            <a href="javascript:form1.submit()" class="rotate-icon"><i class="fa-solid fa-arrow-rotate-left"></i></a>
                        </td>
                    </tr>
                        <?php endforeach; ?>
                        </form>
                </table>
            <?php endif; ?>
            <?php if ($GoneErrMsg): ?>
                <ul class="errors">
                    <li><?= h($cancel_num) ?><?= h($GoneErrMsg) ?></li>
                </ul>
            <?php endif; ?>
            <?php if ($SuccessMsg): ?>
                <ul class="success">
                    <li><?= h($cancel_num) ?><?= h($SuccessMsg) ?></li>
                </ul>
            <?php endif; ?>
        </div>
    </section>
</body>
</html>
