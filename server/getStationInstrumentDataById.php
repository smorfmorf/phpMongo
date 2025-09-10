<?php
require __DIR__ . '/vendor/autoload.php'; // если используешь composer для MongoDB, можно оставить


$siteId = isset($_GET['siteId']) ? $_GET['siteId'] : '237';
$instrumentId = isset($_GET['instrumentId']) ? $_GET['instrumentId'] : '4';

// вызов функции
getStationInstrumentDataById($siteId, $instrumentId);


function getStationInstrumentDataById($siteId, $instrumentId) {
    // Подключение к PostgreSQL
    $dsn = "pgsql:host=10.8.3.180;port=5432;dbname=ferhri.amur;user=postgres;password=qq";
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