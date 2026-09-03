-- Feature テスト用のデータベース (phpunit.xml の DB_DATABASE で使用)
CREATE DATABASE IF NOT EXISTS `diary_test` CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;
GRANT ALL PRIVILEGES ON `diary_test`.* TO 'diary'@'%';
FLUSH PRIVILEGES;
