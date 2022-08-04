<?php
require_once __DIR__ . '/config.php';

// 接続処理を行う関数
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

// エスケープ処理を行う関数
function h($str)
{
    // ENT_QUOTES: シングルクオートとダブルクオートを共に変換する。
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

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

// view_born_info.phpのエラーバリデーション
function view_validate($indivi_num)
{
    $errors = [];

    if (empty($indivi_num)) {
        $errors[] = MSG_INDIVI_REQUIRED;
    }

    // これが関数内でうまく動作しない
    if (empty($errors) &&
        check_gone($indivi_num)) {
        $errors[] = MSG_DONT_WORKING;
    }

    return $errors;
}

// これは使わないかも。上手く行かない
function check_index($indivi_num)
{
    $err = false;

    $working_pigs = find_working_pigs('WORKING'); //稼動中のデータ取得
    $indivi_num_array = array_column($working_pigs,'indivi_num'); // indivi_numを配列化して、インデックスを取り出す
    $index = array_search($indivi_num,$indivi_num_array);
    // var_dump($index);
    // var_dump($indivi_num);
    // var_dump($indivi_num_array);
    if ($index === false) {
        $err = true;
    } else {
        $err =false;
    }
    return $err;
}

// こっちは？
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

