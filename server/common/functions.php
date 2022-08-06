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

    if (empty($errors) &&
        check_day($add_day)) {
        $errors[] = MSG_ADD_DAY_REQUIRED;
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

// get_pig_id関数とセットで使う(get_pig_idのエラー回避)
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

    // if (empty($rotate_condition || $born_num_condition || $pre_rptate_condition)) {
    //     $errors[] = MSG_CONDITION_REQUIRED;
    // }

    // if (empty($born_num_condition)) {
    //     $errors[] = MSG_CONDITION_REQUIRED;
    // }

    // if (empty($pre_rptate_condition)) {
    //     $errors[] = MSG_CONDITION_REQUIRED;
    // }

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

// 日付制限
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

// 入力日が過去の日付か確認する関数
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

// 出産日がadd_dayの後の日付か確認する関数
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
    $add_day = $indivi_info['add_day'];

    // $d_pig_add = new DATETIME($add_day);
    // $considered_time = new DATETIME('+6 month');
    // $pig_age = $considered_time->diff($d_pig_add);
    // return $pig_age->y;

    $time = strtotime($born_day) - strtotime($add_day);

    if ($time < 0) {
        $err = true;
    }

    return $err;
}

// この関数を使い回す
// 入力日が過去の日付か確認する関数
function check_left_day($left_day)
{
    $err = false;
    
    $time = strtotime('now') - strtotime($left_day);

    if ($time < 0) {
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

// ▼更新関数
// 廃用フラグ(gone==1にする関数)
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
// 廃用日の登録
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

// 実績回転数を算出する関数
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

// ▼未使用の関数
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
