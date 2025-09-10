<?php
class Database {
    private static $connections = [];

    public static function connect($dbSection) {
        $config = parse_ini_file('./config.ini', true);

        if (!isset($config[$dbSection])) {
            throw new Exception("Конфигурация '$dbSection' не найдена.");
        }

        $dbConfig = $config[$dbSection];

        $dsn = "pgsql:host={$dbConfig['host']};dbname={$dbConfig['dbname']}";
        $user = $dbConfig['user'];
        $pass = $dbConfig['pass'];

        try {
            self::$connections[$dbSection] = new PDO($dsn, $user, $pass);
            self::$connections[$dbSection]->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Ошибка подключения: " . $e->getMessage());
        }
    }

    public static function getConnection($dbSection) {
        return self::$connections[$dbSection] ?? null;
    }
}


// Подключение к Amur DB
Database::connect('amurDb');
$connAmur = Database::getConnection('amurDb');

// Подключение к SDT DB
Database::connect('SDTDb');
$connSDT = Database::getConnection('SDTDb');

// Тестовый запрос
$stmt = $connAmur->query("SELECT * FROM some_table");
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

print_r($data);
