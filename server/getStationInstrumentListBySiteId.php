<?php
function getStationInstrumentListBySiteId($siteId)
{
    // Словарь siteInstrumentMeta (как в C#)
    $siteInstrumentMeta = [
        1 => "Порт и адрес подключения Telnet",
        4 => "IP адрес поста"
    ];

    // Строка подключения (замени своими данными)
    $dsn = "pgsql:host=10.8.3.180;port=5432;dbname=ferhri.amur;user=postgres;password=qq";

    $stationsData = [];

    try {
        $pdo = new PDO($dsn);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Собираем список ID из словаря
        $ids = implode(",", array_keys($siteInstrumentMeta));

        $sql = "
            SELECT si2.site_id,
                   instrument.id AS instrument_id,
                   si2.location_description,
                   si2.description
            FROM (
                SELECT * FROM meta.site_instrument AS si WHERE si.site_id = :siteId
            ) AS si2
            RIGHT JOIN meta.instrument ON si2.instrument_id = instrument.id
            WHERE instrument.id IN ($ids)
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':siteId', $siteId, PDO::PARAM_INT);
        $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $instrumentId = (int)$row['instrument_id'];
            $stationsData[] = [
                "instrumentId" => $instrumentId,
                "locationDescription" => $siteInstrumentMeta[$instrumentId] ?? "",
                "description" => $row['description'] === null ? "" : $row['description']
            ];
        }
    } catch (Exception $ex) {
        return json_encode([
            "error" => "Произошла ошибка. Сообщение: " . $ex->getMessage()
        ], JSON_UNESCAPED_UNICODE);
    }

    return json_encode($stationsData, JSON_UNESCAPED_UNICODE);
}

header('Content-Type: application/json; charset=utf-8');



// --- Чтение параметров запроса ---
$siteId = $_GET['siteId'] ?? '123';

echo getStationInstrumentListBySiteId($siteId);