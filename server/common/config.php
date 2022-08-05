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
define('MSG_GONE_REQUIRED','廃用日を入力して下さい');
define('MSG_BORN_DAY_REQUIRED','出産日を入力して下さい');
define('MSG_BORN_NUM_REQUIRED','出産頭数を入力して下さい');
define('MSG_INDIVI_DUPLICATE','同一番号の個体が稼動中です');
define('MSG_DONT_WORKING','入力した個体番号は稼動中ではありません');
define('MSG_INFO','個体番号を確認して正しい番号を入力するか、新規母豚登録してください');
define('MSG_CONDITION_REQUIRED','すべての抽出条件を入力して下さい');
define('MSG_BPEP_REQUIRED','始期と終期の両方を入力して下さい');
define('MSG_PERIOD_REQUIRED','日付を正しく入力して下さい(始期<終期、始期<現在)');
define('MSG_LEFT_REQUIRED','日付を正しく入力して下さい(廃用日<現在)');
define('MSG_ID_JUDGEMENT','稼動中の個体番号を入力して下さい');

// サクセスメッセージを定義
define('MSG_INSERT_SUCCESS','の登録が完了しました');

// メニューを定義
define('MSG_INSERT_MENU','母豚の新規登録');
define('MSG_INSERT_BORN_MENU','出産情報の登録');
define('MSG_GONE_MENU','母豚の廃用登録');
define('MSG_VIEW_MENU','稼動中の母豚確認');
define('MSG_VIEW_BORN_MENU','母豚の詳細情報の確認');
define('MSG_CHECK_MENU','不調な母豚を抽出');
define('MSG_PRODUCTIVITY_MENU','全体の生産性の確認');
define('MSG_MANUAL_MENU','取扱説明');
define('MSG_INPORT_MENU','データの取込み');
