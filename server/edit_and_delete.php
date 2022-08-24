<?php 
require_once __DIR__ . '/common/functions.php';

// 初期化
$indivi_num = '';
$check_num = '61-6';
$choose = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $indivi_num = filter_input(INPUT_POST, 'indivi_num');
    $check_num = filter_input(INPUT_POST, 'check_num');
    $choose = filter_input(INPUT_POST, 'edit_or_delete');
    $errors = edit_and_delete_validate($indivi_num);
    $errors = edit_and_delete_validate($check_num);

    if (empty($errors)) {

        if ($choose === 'edit') {
            header('Location: edit_indivi_num.php?indivi_num='.$indivi_num);
            exit;
        }

        if ($choose === 'delete') {
            $pig_id = get_pig_id($indivi_num);
            // delete_indivi_num($pig_id);

        }
    }
}

// 登録した個体を削除する関数
// エラーバリデーション
// 稼動中の個体を入力してください
// 削除する個体の出産情報をすべて削除してから
function delete_indivi_num($id)
{
    $dbh = connect_db();

    $sql = <<<EOM
    DELETE
        FROM
            individual_info
        WHERE
            id = :id;
    EOM;

    $stmt = $dbh->prepare($sql);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
}
// すべての出産情報を削除する関数
function delete_born_info($pig_id)
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

// delete_born_info($pig_id);






$title = '';
?>
出産情報が登録されている場合、削除はできない。
エラー通知を出して、出産情報を全消しして個体番号を削除。
削除前に「本当に削除してよいですか」通知。

<!DOCTYPE html>
<html lang="ja">
<?php include_once __DIR__ . '/_head.php'; ?>
<body>
    <?php include_once __DIR__ . '/_header.php'; ?>

    <section class="edit_content wrapper">
        <h1 class="edit_title">登録ミスの対処法</h1>

        <?php if ($errors): ?>
            <ul class="errors">
                <?php foreach ($errors as $error): ?>
                    <li>
                        <?= h($error) ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

            <h2 class="item">▼登録した個体番号の修正または削除</h2>
            
                <p>個体番号を入力し、修正または削除を選択して下さい</p>
                <form class="" action="" method="POST">
                    <div class="">
                        <input class="edit_and_delete_input" type="text" name="indivi_num" value="">
                        <label for="">修正
                            <input class="radio" type="radio" name="edit_or_delete" value="edit" >
                        </label>
                        <label for="">削除
                            <input class="radio" type="radio" name="edit_or_delete" value="delete">
                        </label>
                        <input type="submit" value="選択" class="flag-btn">
                    </div>
                </form>


            <h2 class="item">▼登録した出産情報の修正または削除</h2>
                <p>個体番号を入力し、出産情報から修正または削除する出産情報を選択して下さい</p>
                <form class="" action="" method="POST">
                    <div class="">
                        <input class="edit_and_delete_input" type="text" name="check_num" value="">
                        <input type="submit" value="選択" class="flag-btn">
                    </div>
                </form>

                <?php if($check_num): ?>
                    <p><?= $check_num ?></p>
                    <table>
                        <tr>
                            <th>出産日</th>
                            <th>産子数</th>
                            <th>修正</th>
                            <th>削除</th>
                        </tr>
                        <tr>
                            <td>2020/2/2</td>
                            <td>5</td>
                            <td>ボタン</td>
                            <td>ボタン</td>
                        </tr>
                        <tr>
                            <td>2022/2/2</td>
                            <td>9</td>
                            <td>ボタン</td>
                            <td>ボタン</td>
                        </tr>
                    </table>
                <?php endif; ?>

            <h2 class="item">▼廃用の取り消し</h2>
            
                <p>個体番号と廃用日を入力して下さい</p>
                <p>廃用日が不明な場合は取り消しできません(問い合わせ)</p>
                <form class="" action="" method="POST">
                    <div class="">
                        <input class="edit_and_delete_input" type="text" name="return_num" value="">
                        <input class="edit_and_delete_input" type="date" name="return_date" value="">
                        
                        <input type="submit" value="選択" class="flag-btn">
                    </div>
                </form>
    
    </section>


</body>
</html>
