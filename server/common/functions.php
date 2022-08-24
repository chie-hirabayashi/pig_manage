<?php
require_once __DIR__ . '/config.php';

// ライブラリ読込(エクセルデータのインポートで使用)
require './vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet;

// ▼接続処理を行う関数
function connect_db()
{
    try {
        return new PDO(
            DSN,
            USER,
            PASSWORD,
            [PDO::ATTR_ERRMODE =>
            PDO::ERRMODE_EXCEPTION]
        );
    } catch (PDOException $e) {
        echo $e->getMessage();
        exit;
    }
}

// ▼エスケープ処理を行う関数
function h($str)
{
    // ENT_QUOTES: シングルクオートとダブルクオートを共に変換する。
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

// ▼バリデーション関数
// insert.phpのエラーバリデーション
function insert_validate($indivi_num, $add_day)
{
    $errors = [];

    if (empty($indivi_num)) {
        $errors[] = MSG_INDIVI_REQUIRED;
    }

    if (empty($add_day)) {
        $errors[] = MSG_ADD_REQUIRED;
    }

    if (empty($errors) &&
        check_duplication($indivi_num)) {
        $errors[] = MSG_INDIVI_DUPLICATE;
    }

    if (empty($errors) &&
        check_day($add_day)) {
        $errors[] = MSG_ADD_DAY_REQUIRED;
    }

    return $errors;
}
// edit_and_delete.phpのエラーバリデーション
function edit_and_delete_validate($indivi_num)
{
    $errors = [];

    if (empty($indivi_num)) {
        $errors[] = MSG_INDIVI_REQUIRED;
    }

    if (empty($errors) &&
        check_pig_id($indivi_num)) {
        $errors[] = MSG_ID_JUDGEMENT;
    }

    return $errors;
}
// gone.phpのエラーバリデーション
function gone_validate($indivi_num, $left_day)
{
    $errors = [];

    if (empty($indivi_num)) {
        $errors[] = MSG_INDIVI_REQUIRED;
    }

    if (empty($left_day)) {
        $errors[] = MSG_GONE_REQUIRED;
    }

    if (empty($errors) &&
        check_day($left_day)) {
        $errors[] = MSG_LEFT_REQUIRED;
    }

    if (empty($errors) &&
        check_pig_id($indivi_num)) {
        $errors[] = MSG_ID_JUDGEMENT;
    }

    if (empty($errors) &&
        check_add_day($indivi_num,$left_day)) {
        $errors[] = MSG_LEFT_REQUIRED;
    }

    return $errors;
}
// get_pig_id関数とセットで使う(get_pig_idのエラー回避:$indivi_numが稼動中か確認)
// gone_validate,view_validateで使用
function check_pig_id($indivi_num)
{
    $err = false;
    
    $dbh = Connect_db();

    $sql = <<<EOM
    SELECT 
        * 
    FROM 
        individual_info
    WHERE 
        indivi_num = :indivi_num
    EOM;
    
    $stmt = $dbh->prepare($sql);
    $stmt->bindValue(':indivi_num', $indivi_num, PDO::PARAM_STR);
    $stmt->execute();

    $indivi_info = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $judge = in_array(0,array_column($indivi_info,"gone"));
    if ($judge == false) {
        $err = true;
    }

    return $err;
}
// check.phpのエラーバリデーション
function check_validate($rotate_condition, $born_num_condition, $pre_rptate_condition)
{
    $errors = [];

    if (empty($rotate_condition)) {
        $errors[] = MSG_CONDITION_REQUIRED;
    } elseif (empty($born_num_condition)) {
        $errors[] = MSG_CONDITION_REQUIRED;
    } elseif (empty($pre_rptate_condition)) {
        $errors[] = MSG_CONDITION_REQUIRED;
    }

    return $errors;
}
// productivity.phpのエラーバリデーション
function period_validate($bp, $ep)
{
    $errors = [];

    if (empty($bp)) {
        $errors[] = MSG_BPEP_REQUIRED;
    } elseif (empty($ep)) {
        $errors[] = MSG_BPEP_REQUIRED;
    }

    if (empty($errors)) {
        if (check_period($bp,$ep)) {
            $errors[] = MSG_PERIOD_REQUIRED;
        }
    }

    return $errors;
}
// 日付制限(現在<始期<終期)
// period_validateで使用
function check_period($bp,$ep)
{
    $err = false;
    
    $bp_ep = strtotime($ep) - strtotime($bp);
    $time = strtotime('now') - strtotime($bp);

    if ($bp_ep <= 0 || $time < 0) {
        $err = true;
    }

    return $err;
}
// 日付制限(過去の日付か確認する関数)
// insert_validate,gone_bvalidete,insert_born_valodateで使用
function check_day($day)
{
    $err = false;
    
    $time = strtotime('now') - strtotime($day);

    if ($time < 0) {
        $err = true;
    } else {
        $err = false;
    }
    return $err;
}
// 日付制限(出産日がadd_dayの後の日付か確認する関数)
// gone_validate,insert_born_validateで使用
function check_add_day($indivi_num,$born_day)
{
    $err = false;

    $dbh = Connect_db();

    $sql = <<<EOM
    SELECT 
        * 
    FROM 
        individual_info
    WHERE 
        indivi_num = :indivi_num;
    EOM;
    
    $stmt = $dbh->prepare($sql);
    $stmt->bindValue(':indivi_num', $indivi_num, PDO::PARAM_STR);
    $stmt->execute();

    $indivi_info = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!empty($indivi_info)) {
        $add_day = $indivi_info['add_day'];

        $time = strtotime($born_day) - strtotime($add_day);

        if ($time < 0) {
            $err = true;
        }
    }

    return $err;
}
// view_born_info.phpのエラーバリデーション
function view_validate($indivi_num)
{
    $errors = [];

    if (empty($indivi_num)) {
        $errors[] = MSG_INDIVI_REQUIRED;
    }

    if (empty($errors) &&
        check_pig_id($indivi_num)) {
        $errors[] = MSG_DONT_WORKING;
    }

    return $errors;
}
// insert_born_info.phpのエラーバリデーション
function insert_born_validate($indivi_num, $born_day, $born_num)
{
    $errors = [];

    if (empty($indivi_num)) {
        $errors[] = MSG_INDIVI_REQUIRED;
    }

    if (empty($born_day)) {
        $errors[] = MSG_BORN_DAY_REQUIRED;
    }

    if (empty($born_num)) {
        $errors[] = MSG_BORN_NUM_REQUIRED;
    }

        if (empty($errors) &&
        check_day($born_day)) {
        $errors[] = MSG_BORN_D_REQUIRED;
    }

    if (empty($errors) &&
        check_add_day($indivi_num,$born_day)) {
        $errors[] = MSG_BORN_D_REQUIRED;
    }

    return $errors;
}
// indivi_numの重複確認(gone='WORKING'状態の同一番号はNG)
// insert_validateで使用
function check_duplication($indivi_num)
{
    $err = false;

    $dbh = connect_db();

    $sql = <<<EOM
    SELECT
        *
    FROM
        individual_info
    WHERE
        indivi_num = :indivi_num;
    EOM;

    $stmt = $dbh->prepare($sql);
    $stmt->bindValue(':indivi_num', $indivi_num, PDO::PARAM_STR);
    $stmt->execute();

    $duplication_nums = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // $duplication_numがemptyじゃなくて、かつ、WORKINGがいる場合error
    if (!empty($duplication_nums)) {
        // 重複番号が稼働中か確認
        $duplication_list = [];
        foreach ($duplication_nums as $duplication_num) {
            if ($duplication_num['gone'] === 0){
                $duplication_list[] = 1;
            }
        }
        if (in_array(1,$duplication_list)) {
            $err = true;
        }else {
            $err = false;
        }
    return $err;
    }
}

// ▼インサート関数
// 新規母豚登録 insert.phpで使用
function insert_pig($indivi_num, $add_day)
{
    $dbh = connect_db();

    $sql = <<<EOM
    INSERT INTO
        individual_info
        (indivi_num, add_day)
    VALUES
        (:indivi_num, :add_day)
    EOM;

    $stmt = $dbh->prepare($sql);
    $stmt->bindValue(':indivi_num', $indivi_num, PDO::PARAM_STR);
    $stmt->bindValue(':add_day', $add_day, PDO::PARAM_STR);

    $stmt->execute();
}
// 出産情報登録 insert_born_infoで使用
function insert_born_info($pig_id, $born_day, $born_num)
{
    $dbh = connect_db();

    $sql = <<<EOM
    INSERT INTO
        born_info
        (pig_id, born_day, born_num)
    VALUES
        (:pig_id, :born_day, :born_num)
    EOM;

    $stmt = $dbh->prepare($sql);
    $stmt->bindValue(':pig_id', $pig_id, PDO::PARAM_INT);
    $stmt->bindValue(':born_day', $born_day, PDO::PARAM_STR);
    $stmt->bindValue(':born_num', $born_num, PDO::PARAM_INT);

    $stmt->execute();
}

// ▼更新関数
// 廃用フラグ(goneを変更する関数) gone.phpで使用
function update_gone($id, $status)
{
    $dbh = connect_db();

    $sql = <<<EOM
    UPDATE
        individual_info
    SET
        gone = :status
    WHERE
        id = :id
    EOM;

    $stmt = $dbh->prepare($sql);

    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->bindValue(':status', $status, PDO::PARAM_INT);

    $stmt->execute();
}
// 廃用日の登録 gone.phpで使用
function update_left_day($indivi_num, $left_day)
{
    $dbh = connect_db();

    $sql = <<<EOM
    UPDATE
        individual_info
    SET
        left_day = :left_day
    WHERE
        indivi_num = :indivi_num
    EOM;

    $stmt = $dbh->prepare($sql);

    $stmt->bindValue(':indivi_num', $indivi_num, PDO::PARAM_STR);
    $stmt->bindValue(':left_day', $left_day, PDO::PARAM_STR);

    $stmt->execute();
}
// 要観察フラグ check_indivi_info.php,view_indivi_info.phpで使用
function flag($id, $status)
{
    $dbh = connect_db();

    $sql = <<<EOM
    UPDATE
        individual_info
    SET
        flag = :status
    WHERE
        id = :id
    EOM;

    $stmt = $dbh->prepare($sql);

    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->bindValue(':status', $status, PDO::PARAM_INT);

    $stmt->execute();
}

// ▼取得関数
// flag情報を取得 check_result.php,check_indivi_info.php,view_indivi_info.phpで使用
function find_flag_info($id)
{
    $dbh = Connect_db();

    $sql = <<<EOM
    SELECT 
        flag 
    FROM 
        individual_info
    WHERE 
        id = :id
    EOM;

    $stmt = $dbh->prepare($sql);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    $flag_info = $stmt->fetch(PDO::FETCH_ASSOC);
    return $flag_info['flag'];
}

// ▼抽出、詳細情報確認に必要な関数
// indivi_numから年齢を取得する関数 check.php,chesk_result.phpで使用
function get_age($indivi_num)
{
    $dbh = connect_db();

    $sql = <<<EOM
    SELECT 
        * 
    FROM 
        individual_info
    WHERE 
        indivi_num = :indivi_num;
    EOM;
    
    $stmt = $dbh->prepare($sql);
    $stmt->bindValue(':indivi_num', $indivi_num, PDO::PARAM_STR);
    $stmt->execute();

    $indivi_info = $stmt->fetch(PDO::FETCH_ASSOC);
    $add_day = $indivi_info['add_day'];

    $d_pig_add = new DATETIME($add_day);
    $considered_time = new DATETIME('+6 month');
    $pig_age = $considered_time->diff($d_pig_add);
    return $pig_age->y;
}


// indivi_numからidを取得する関数
// check_pig_idとセットで使う
function get_pig_id($indivi_num)
{
    $dbh = Connect_db();

    $sql = <<<EOM
    SELECT 
        * 
    FROM 
        individual_info
    WHERE 
        indivi_num = :indivi_num
    AND
        gone = 0;
    EOM;
    
    $stmt = $dbh->prepare($sql);
    $stmt->bindValue(':indivi_num', $indivi_num, PDO::PARAM_STR);
    $stmt->execute();

    $indivi_info = $stmt->fetch(PDO::FETCH_ASSOC);
    $pig_id = $indivi_info['id'];
    return $pig_id;
}

// 実績回転数を算出する関数(抽出用)
function get_rotate($pig_id)
{
    $born_info = find_born_info($pig_id);
    // 回転数算出(直近の回転数を算出)
    $count_info_num = count($born_info);
    if ($count_info_num == 0) {
        $rotate = 99.9;
    } elseif ($count_info_num == 1) {
        $rotate = 99.9;
    } else {
        $born_day1 = new DateTime($born_info[0]['born_day']);
        $born_day2 = new DateTime($born_info[1]['born_day']);
        $span = $born_day1->diff($born_day2);
        $rotate = round(365 / $span->days, 2);
    }
    return $rotate;
}

// 実績回転数を算出する関数(抽出用)
function view_rotate($pig_id)
{
    $born_info = find_born_info($pig_id);
    // 回転数算出(直近の回転数を算出)
    $count_info_num = count($born_info);
    if ($count_info_num == 0) {
        $rotate = 0;
    } elseif ($count_info_num == 1) {
        $rotate = 1;
    } else {
        $born_day1 = new DateTime($born_info[0]['born_day']);
        $born_day2 = new DateTime($born_info[1]['born_day']);
        $span = $born_day1->diff($born_day2);
        $rotate = round(365 / $span->days, 2);
    }
    return $rotate;
}

// 過去2回の産子数を算出する関数
function get_born_num($pig_id)
{
    $born_info = find_born_info($pig_id);
    // 産子数算出
    $count_info_num = count($born_info);
    if ($count_info_num == 0) {
        $born_num1 = 99;
        $born_num2 = 99;
    } elseif ($count_info_num == 1) {
        $born_num1 = $born_info[0]['born_num'];
        $born_num2 = '(-)';
    } else {
        $born_num1 = $born_info[0]['born_num'];
        $born_num2 = $born_info[1]['born_num'];
    }
    $born_num_l[] = $born_num1;
    $born_num_l[] = $born_num2;

    return $born_num_l;
}

// 予測回転数を算出する関数
function get_predict_rotate($pig_id)
{
    $born_info = find_born_info($pig_id);
    // 回転数算出(直近の回転数を算出)
    $count_info_num = count($born_info);
    if ($count_info_num == 0) {
        $predict_rotate = 99.9; //add_dayを起点にして算出するか？
    } else {
        $born_day0 = new DateTime();
        $born_day1 = new DateTime($born_info[0]['born_day']);
        $span = $born_day0->diff($born_day1);
        $predict_rotate = round(365 / $span->days, 2);
    }
    return $predict_rotate;
}

// idから個体情報を取得する
function find_indivi_info($id)
{
    $dbh = Connect_db();

    $sql = <<<EOM
    SELECT 
        * 
    FROM 
        individual_info
    WHERE 
        id = :id;
    EOM;
    
    $stmt = $dbh->prepare($sql);

    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    $indivi_info = $stmt->fetch(PDO::FETCH_ASSOC);

    return $indivi_info;
}

// すべての回転数算出
function get_rotate_l($pig_id)
{
    $born_info = find_born_info($pig_id);
    $count_info_num = count($born_info);

    $born_days = [];
    foreach ($born_info as $one_info) {
        $born_days[] = $one_info['born_day'];
    }

    $rotate_list = [];
    for ($i=0; $i < $count_info_num-1 ; $i++) { 
    $born_day1 = new DateTime($born_info[$i]['born_day']);
    $born_day2 = new DateTime($born_info[$i+1]['born_day']);
    $span = $born_day1->diff($born_day2);
    $one_rotate = round(365 / $span->days, 2);

    $rotate_list[] = $one_rotate;
    }
    return $rotate_list;
}

// 年齢取得
function pig_age($pig_id)
{
    $the_indivi_info = find_indivi_info($pig_id);
    $pig_add_day = $the_indivi_info['add_day'];

    $d_pig_add = new DATETIME($pig_add_day);
    $considered_time = new DATETIME('+6 month');

    $pig_age = $considered_time->diff($d_pig_add);
    $pig_age = $pig_age->y;
    return $pig_age;
}

// ▼修正関数
// edit_indivi_num.phpで使用
function edit_indivi_num($id, $after_indivi_num)
{
    $dbh = connect_db();

    $sql = <<<EOM
    UPDATE
        individual_info
    SET
        indivi_num = :after_indivi_num
    WHERE
        id = :id
    EOM;

    $stmt = $dbh->prepare($sql);

    $stmt->bindValue(':id',$id, PDO::PARAM_INT);
    $stmt->bindValue(':after_indivi_num',$after_indivi_num, PDO::PARAM_STR);

    $stmt->execute();
}


// ▼エクセルデータのインポート
function import_db_individual_info($import_file)
{
    // [****.xlsx] ファイルをロードしSpreadsheetオブジェクト作成
    $objSpreadsheet = IOFactory::load('./import/' . $import_file);

    $objSheet = $objSpreadsheet->getSheet(0); // 読み込むシートを指定

    // ワークシート内の最大領域座標（"A1:XXXnnn" XXX:最大カラム文字列, nnn:最大行）
    $strRange = $objSheet->calculateWorksheetDimension();

    // ワークシートの全てのデータ取得（配列データとして）
    $arrData = $objSheet->rangeToArray($strRange);

    // 取得確認
    // var_dump($arrData);
    // echo '<pre>';
    // print_r($arrData);
    // echo '</pre>';

    $dbh = connect_db();

    foreach ($arrData as $data){
        $id = $data[0];
        $indivi_num = $data[1];
        $add_day = $data[2];
        $left_day = $data[3];
        $gone = $data[4];

        $d_add = new DateTime($add_day);

        $sql = <<<EOM
        INSERT INTO
            individual_info
            (id, indivi_num, add_day, left_day, gone)
        VALUES
            (:id, :indivi_num, :add_day, :left_day, :gone)
        EOM;

        $stmt = $dbh->prepare($sql);

        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':indivi_num', $indivi_num, PDO::PARAM_STR);
        $stmt->bindValue(':add_day', $d_add->format('Y-m-d'), PDO::PARAM_STR);
        if(!empty($left_day)){
            $d_left = new DateTime($left_day);
            $stmt->bindValue(':left_day', $d_left->format('Y-m-d'), PDO::PARAM_STR);
        }else{
            $stmt->bindValue(':left_day', null, PDO::PARAM_STR);
        }
        $stmt->bindValue(':gone', $gone, PDO::PARAM_INT);

        $stmt->execute();
    }
}

function import_db_born_info($import_file)
{
    // [****.xlsx] ファイルをロードしSpreadsheetオブジェクト作成
    $objSpreadsheet = IOFactory::load('./import/' . $import_file);

    $objSheet = $objSpreadsheet->getSheet(1); // 読み込むシートを指定

    // ワークシート内の最大領域座標（"A1:XXXnnn" XXX:最大カラム文字列, nnn:最大行）
    $strRange = $objSheet->calculateWorksheetDimension();

    // ワークシートの全てのデータ取得（配列データとして）
    $arrData = $objSheet->rangeToArray($strRange);

    $dbh = connect_db();

    foreach ($arrData as $data){
        $id = $data[0];
        $pig_id = $data[1];
        $born_day = $data[2];
        $born_num = $data[3];

        $d_born = new DateTime($born_day);

        $sql = <<<EOM
        INSERT INTO
            born_info
            (id, pig_id, born_day, born_num)
        VALUES
            (:id, :pig_id, :born_day, :born_num)
        EOM;

        $stmt = $dbh->prepare($sql);

        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':pig_id', $pig_id, PDO::PARAM_STR);
        $stmt->bindValue(':born_day', $d_born->format('Y-m-d'), PDO::PARAM_STR);
        $stmt->bindValue(':born_num', $born_num, PDO::PARAM_INT);

        $stmt->execute();
    }
}

function import_validate($import_file)
{
    $errors = [];

    if (empty($import_file)) {
        $errors[] = MSG_NO_FILE;
    } else {
        if (check_file_ext($import_file)) {
            $errors[] = MSG_NOT_ABLE_EXT;
        }
    }

    return $errors;
}

function check_file_ext($import_file)
{
    $err = false;

    $file_ext = pathinfo($import_file, PATHINFO_EXTENSION); //拡張子がとれる
    if ($file_ext != 'xlsx') {
        $err = true;
    }

    return $err;
}

// ▼未使用の関数
// born_info全データ削除
function delete_all_born_info()
{
    $dbh = connect_db();

    $sql = <<<EOM
    DELETE 
        FROM 
    born_info
    EOM;

    $stmt = $dbh->prepare($sql);
    $stmt->execute();
}

// individual_info全データ削除
function delete_all_individual_info()
{
    $dbh = connect_db();

    $sql = <<<EOM
    DELETE 
        FROM 
    individual_info
    EOM;

    $stmt = $dbh->prepare($sql);
    $stmt->execute();
}
// すべての個体データを取得する
function find_all_indivi_infos()
{
    $dbh = connect_db();

    $sql = 'SELECT * FROM individual_info';
    $stmt = $dbh->prepare($sql);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// すべての出産データを取得する
function find_all_born_infos()
{
    $dbh = connect_db();

    $sql = <<<EOM
    SELECT 
        * 
    FROM 
        born_info
    ORDER BY
        born_day DESC;
    EOM;

    $stmt = $dbh->prepare($sql);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// 年齢取得
function age($add_day)
{
    $d_pig_add = new DATETIME($add_day);
    $considered_time = new DATETIME('+6 month');
    $pig_age = $considered_time->diff($d_pig_add);
    return $pig_age->y;
}

// 廃用済みの個体番号でエラー
function check_gone($indivi_num)
{
    $err = false;

    $dbh = connect_db();

    $sql = <<<EOM
    SELECT
        *
    FROM
        individual_info
    WHERE
        indivi_num = :indivi_num;
    EOM;

    $stmt = $dbh->prepare($sql);
    $stmt->bindValue(':indivi_num', $indivi_num, PDO::PARAM_STR);
    $stmt->execute();

    $duplication_nums = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // $numsがemptyまたはWORKINGがいない場合error
    if (!empty($duplication_nums)) {
        $duplication_list = [];
        foreach ($duplication_nums as $duplication_num) {
            if ($duplication_num['gone'] === 0){
                $duplication_list[] = 1;
            }
        }
        if (in_array(1,$duplication_list)) {
            $err = false;
        }else {
            $err = true;
        }
    }
    return $err;
}
// 稼動中のすべての個体データを取得する
function find_working_pigs($gone)
{
    $dbh = connect_db();

    $sql = <<<EOM
    SELECT 
        * 
    FROM 
        individual_info
    WHERE 
        gone = :gone;
    EOM;
    
    $stmt = $dbh->prepare($sql);
    $stmt->bindValue(':gone', $gone, PDO::PARAM_STR);
    $stmt->execute();

    // return $stmt->fetch(PDO::FETCH_ASSOC);
    $working_pigs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $working_pigs;
}
// フラグの立った個体データを取得する
function find_flag_pigs($status)
{
    $dbh = connect_db();

    $sql = <<<EOM
    SELECT 
        * 
    FROM 
        individual_info
    WHERE 
        flag = :status;
    EOM;
    
    $stmt = $dbh->prepare($sql);
    $stmt->bindValue(':status', $status, PDO::PARAM_STR);
    $stmt->execute();

    $flag_pigs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $flag_pigs;
}
// 出産情報を取得
function find_born_info($pig_id)
{
    $dbh = Connect_db();

    $sql = <<<EOM
    SELECT 
        * 
    FROM 
        born_info
    WHERE 
        pig_id = :pig_id
    ORDER BY
        born_day DESC;
    EOM;

    $stmt = $dbh->prepare($sql);
    $stmt->bindValue(':pig_id', $pig_id, PDO::PARAM_INT);
    $stmt->execute();

    $the_born_info = $stmt->fetchAll(PDO::FETCH_ASSOC);
    return $the_born_info;
}
