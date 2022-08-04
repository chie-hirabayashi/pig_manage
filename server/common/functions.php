<?php
require_once __DIR__ . '/config.php';

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

    return $errors;
}

// check.phpのエラーバリデーション
function check_validate($rotate_condition, $born_num_condition, $pre_rptate_condition)
{
    $errors = [];

    if (empty($rotate_condition || $born_num_condition || $pre_rptate_condition)) {
        $errors[] = MSG_CONDITION_REQUIRED;
    }

    return $errors;
}

// productivity.phpのエラーバリデーション
function period_validate($bp, $ep)
{
    $errors = [];

    if (empty($bp || $ep)) {
        $errors[] = MSG_BPEP_REQUIRED;
    }

    if (empty($errors &&
        check_period($bp,$ep))) {
        $errors[] = MSG_PERIOD_REQUIRED;
    }

    return $errors;
}

function check_period($bp,$ep)
{
    $err = false;
    
    $bp_ep = strtotime($ep) - strtotime($bp);
    $time = strtotime('now') - strtotime($bp);

    if ($bp_ep/86400 <= 0 || $time < 0) {
        $err = true;
    } else {
        $err = false;
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
        check_gone($indivi_num)) {
        $errors[] = MSG_DONT_WORKING;
    }

    return $errors;
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

    return $errors;
}


// indivi_numの重複確認(gone='WORKING'状態の同一番号はNG)
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
// 新規母豚登録
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

// 出産情報登録
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

// ▼取得関数
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

// ▼抽出に必要な関数
// indivi_numから年齢を取得する関数
function get_age($indivi_num)
{
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
    $add_day = $indivi_info['add_day'];

    $d_pig_add = new DATETIME($add_day);
    $considered_time = new DATETIME('+6 month');
    $pig_age = $considered_time->diff($d_pig_add);
    return $pig_age->y;
}

// indivi_numからpig_idを取得する関数
function get_pig_id($indivi_num)
{
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
    $pig_id = $indivi_info['id'];
    return $pig_id;
}

// 実績回転数を算出する関数
function get_rotate($pig_id)
{
    $born_info = find_born_info($pig_id);
    // 回転数算出(直近の回転数を算出)
    $count_info_num = count($born_info);
    if ($count_info_num == 0) {
        $rotate = '(-)';
    } elseif ($count_info_num == 1) {
        $rotate = 1.0;
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
        $born_num1 = '(-)';
        $born_num2 = '(-)';
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
        $predict_rotate = '(-)'; //add_dayを起点にして算出するか？
    } else {
        $born_day0 = new DateTime();
        $born_day1 = new DateTime($born_info[0]['born_day']);
        $span = $born_day0->diff($born_day1);
        $predict_rotate = round(365 / $span->days, 2);
    }
    return $predict_rotate;
}


// ▼未使用の関数
// 個体番号から個体情報を取得する(これはNG関数:個体番号に重複があるため目的物を正確に取得できない)
function find_indivi_info($indivi_num)
{
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
    return $indivi_info;
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
