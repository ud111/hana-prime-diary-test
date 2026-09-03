-- Feature テスト用のデータベース (phpunit.xml の DB_DATABASE で使用)
--
-- 注意: このディレクトリの SQL は MySQL のデータディレクトリが空のとき
-- (= ボリューム hana_prime_diary_test_db_data の初回作成時) にだけ実行されます。
-- あとから書き換えても既存のボリュームには反映されません。
CREATE DATABASE IF NOT EXISTS `diary_test` CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;
GRANT ALL PRIVILEGES ON `diary_test`.* TO 'diary'@'%';
FLUSH PRIVILEGES;
