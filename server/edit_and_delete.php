<?php 
require_once __DIR__ . '/common/functions.php';

// 初期化
$indivi_num = '';
$choose = '';


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $indivi_num = filter_input(INPUT_POST, 'indivi_num');
    $choose = filter_input(INPUT_POST, 'edit_or_delete');
    $pig_id = get_pig_id($indivi_num);

    if ($choose === 'edit') {
        header('Location: edit_indivi_num.php?indivi_num='.$indivi_num);
        exit;
    }
}
        
var_dump($choose);

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

// delete_indivi_num($pig_id);
// delete_born_info($pig_id);






$title = '';
?>

<!DOCTYPE html>
<html lang="ja">
<?php include_once __DIR__ . '/_head.php'; ?>
<body>
    <?php include_once __DIR__ . '/_header.php'; ?>

    <section class="menu_content wrapper">
        <h1>登録ミスの対処法</h1>
            <h2 class="">▼登録した母豚の修正または削除</h2>
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
                <!-- if editでフォームが出現 -->
                <?php if($_SERVER['REQUEST_METHOD'] === 'POST' && $choose=='edit'): ?>

        
                    <!-- <form class="" action="edit_indivi_num.php?befor_indivi_num=<?= 'befor_indivi_num' ?>&aftor_indivi_num=<?= 'befor_indivi_num' ?>" method="POST"> -->
                        <!-- <label for="">修正前の個体番号</label>
                        <input class="edit_and_delete_input" type="text" name="befor_indivi_num" value=<?= $indivi_num ?>><br>
                        <label for="">修正後の個体番号</label>
                        <input class="edit_and_delete_input" type="text" name="after_indivi_num" value=""><br>
                        <input type="submit" value="修正" class="flag-btn"> -->
                <?php endif; ?>
                
                </form>
            <!-- <div class="edit_and_delete_button_area">
                <a href="" class="edit_and_delete_page_button">登録した母豚の修正または削除</a>
                <a href="" class="edit_and_delete_page_button">登録した出産情報の修正または削除</a>
            </div> -->
    
    
    </section>


</body>
</html>
