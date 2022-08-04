<?php
// 接続に必要な情報を定数として定義
define('DSN', 'mysql:host=db;dbname=pig_db;charset=utf8');
define('USER', 'pig_admin');
define('PASSWORD', '0000');

// 廃用を1とする
define('WORKING', 0);
define('GONE', 1);

// エラーメッセージを定義
define('MSG_INDIVI_REQUIRED','個体番号を入力して下さい');
define('MSG_ADD_REQUIRED','導入日を入力して下さい');
define('MSG_BORN_DAY_REQUIRED','出産日を入力して下さい');
define('MSG_BORN_NUM_REQUIRED','出産頭数を入力して下さい');
define('MSG_INDIVI_DUPLICATE','同一番号の個体が稼動中です');
define('MSG_DONT_WORKING','入力した個体番号は稼動中ではありません');
define('MSG_INFO','個体番号を確認して正しい番号を入力するか、新規母豚登録してください');
define('MSG_CONDITION_REQUIRED','すべての抽出条件を入力して下さい');

// サクセスメッセージを定義
define('MSG_INSERT_SUCCESS','の登録が完了しました');
