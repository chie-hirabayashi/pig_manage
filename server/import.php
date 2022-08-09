<?php
// 関数読み込み
require_once __DIR__ . '/common/functions.php';


// ライブラリ読込(エクセルデータのインポートで使用)
require './vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet;

// 初期化

$import_dir = './import';
// バリデーション

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $import_file = filter_input(INPUT_POST, 'import_file');
    // $errors = view_validate($indivi_num);

    // $description = filter_input(INPUT_POST, 'description');
    // サーバー上で一時的に保存されるテンポラリファイル名
    $import_file = $_FILES['import_file']['name'];

    $import_tmp_file = $_FILES['import_file']['tmp_name'];

    // バリデーション(存在チェックと拡張子チェック)
    $errors = import_validate($import_file);

    if (empty($errors)) {
        $import_file = date("Ymd-His") . "_" . $import_file;
        $path = 'import/' . $import_file;

        if (move_uploaded_file($import_tmp_file, $path)) {
            delete_all_born_info(); //born_info全レコード削除
            delete_all_individual_info(); //individual_info全レコード削除
            import_db_individual_info($import_file);
            import_db_born_info($import_file);
            $import_msg = 'データを取込みました';
        } else {
            $import_msg = 'データの取込みができませんでした';
        }
    }
}

$title = '確認menu';
?>

<!DOCTYPE html>
<html lang="ja">
<?php include_once __DIR__ . '/_head.php'; ?>
<body>
    <?php include_once __DIR__ . '/_header.php'; ?>

    <section class="insert_content wrapper">
        <h1 class="insert_title">インポート</h1>

            <!-- <input class="input_file" type="file" id="file_upload" name="image" onchange="imgPreView(event)">
            <textarea class="input_text" name="description" rows="5" placeholder="画像の詳細を入力してください"><?= h($description) ?></textarea>
            <input class="upload_submit" type="submit" value="追加"> -->
        
        <form action="" method="post" class="insert_form" enctype="multipart/form-data">
            <label class="indivi_label" for="indeivi_num">取り込むデータ</label>
            <input class="normal_input" type="file" name="import_file" id="import_file" placeholder="pig_db.xlsx">
            <div class="button_area">
                <input type="submit" value="読み込み" class="insert_button"><br>
                <!-- <a href="view.php" class="view_page_button">登録内容の確認はこちら</a> -->
            </div>
        </form>

        <?php if (empty($errors)): ?>
            <ul class="success">
                <li><?= h($import_msg)?></li>
            </ul>
        <?php endif; ?>
    </section>

    <?php include_once __DIR__ . '/_footer.php'; ?>
</body>
</html>
