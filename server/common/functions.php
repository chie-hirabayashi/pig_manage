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

// insert.php
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
    // return $duplication_nums;
    // $new_pig_numがemptyじゃなくて、かつ、WORKINGがいる場合error
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

// 稼働中確認  ここからだ！！これは不要かも
function check_working($indivi_num)
{
    // $err = false;

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

    $new_pig_num = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!empty($new_pig_num)) {
        // ここにワンクッション重複番号が稼働中華確認
        $err = true;
    }
    return $err;
}

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

// 個体番号から個体情報(pig_id)を取得する
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

