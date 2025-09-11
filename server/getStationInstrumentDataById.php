<?php
require __DIR__ . '/vendor/autoload.php'; // если используешь composer для MongoDB, можно оставить


//! Конфиг 
$config = parse_ini_file(__DIR__ . '/db/config.ini', true);

// * PostgreSQL
$host_Postgres = $config['amurDb']['host'];
$dbName_Postgres = $config['amurDb']['dbname'];
$user_Postgres = $config['amurDb']['user'];
$pass_Postgres = $config['amurDb']['pass'];
$port_Postgres = $config['amurDb']['port'];



$siteId = isset($_GET['siteId']) ? $_GET['siteId'] : '';
$instrumentId = isset($_GET['instrumentId']) ? $_GET['instrumentId'] : '';

// вызов функции
getStationInstrumentDataById($siteId, $instrumentId);


function getStationInstrumentDataById($siteId, $instrumentId) {

    // Подключение к PostgreSQL
    global $host_Postgres, $port_Postgres, $dbName_Postgres, $user_Postgres, $pass_Postgres;
    $dsn = sprintf(
        "pgsql:host=%s;port=%s;dbname=%s;user=%s;password=%s",
        $host_Postgres,
        $port_Postgres,
        $dbName_Postgres,
        $user_Postgres,
        $pass_Postgres
    );


    $result = "";

    try {
        $pdo = new PDO($dsn);

        // SQL запрос
        $sql = "
            SELECT description
            FROM meta.site_instrument
            WHERE site_id = :siteId
              AND instrument_id = :instrumentId
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            "siteId" => $siteId,
            "instrumentId" => $instrumentId
        ]);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $result = $row["description"];
        }
    } 
    catch (Exception $e) {
        // Возврат ошибки в JSON
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            "error" => "Произошла ошибка: " . $e->getMessage()
        ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    // Возврат результата в JSON
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
}