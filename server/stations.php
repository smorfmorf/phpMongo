<?php
require __DIR__ . '/vendor/autoload.php'; // важно!

$region = isset($_GET['region']) ? $_GET['region'] : 27;
$type = isset($_GET['type']) ? $_GET['type'] : "1,5";


function getStationsListLocal($region, $type) {
    // Лог
    error_log("Getting list of sites");

    $finalDataList = [];

    try {
        // --- MongoDB подключение ---
        $mongo = new MongoDB\Client("mongodb://localhost:27017");
        $db = $mongo->selectDatabase("sdt"); // название базы
        $sitesCol = $db->selectCollection("sites");

        // Получаем все сайты
        $sitesCursor = $sitesCol->find();
        $sites = iterator_to_array($sitesCursor);

        // Подключаемся к PostgreSQL
        $dsn = "pgsql:host=10.8.3.180;port=5432;dbname=ferhri.amur;user=postgres;password=qq";
        $pdo = new PDO($dsn);

        // Собираем ids сайтов
        $siteIds = array_map(fn($s) => (int)$s['_id'], $sites);
        $siteIdsStr = implode(",", $siteIds);

        // SQL запрос
        $sql = "
            SELECT 
                station.id,
                station.code,
                station.name,
                station.station_type_id,
                site.id AS site_id,
                site.site_type_id,
                site.description AS site_description,
                station.addr_region_id,
                station_type.name_short AS station_type_name
            FROM meta.station
            LEFT JOIN meta.site 
                ON station.id = site.station_id 
               AND site.site_type_id IN($type)
            LEFT JOIN meta.station_type 
                ON site.site_type_id = station_type.id
            WHERE site.id IN ($siteIdsStr)
              AND station.addr_region_id = :region
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->execute(['region' => $region]);
        $stationsList = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Сопоставляем станции с сайтами
        foreach ($stationsList as $station) {
            foreach ($sites as $site) {
                if ((int)$site['_id'] === (int)$station['site_id']) {
                    $finalDataList[] = [
                        "siteId"        => $site['_id'],
                        "stationCode"   => $site['stationCode'] ?? null,
                        "stationName"   => $site['stationName'] ?? null,
                        "stationType"   => $station['station_type_name'],
                        "stationRegion" => (int)$station['addr_region_id'],
                        "pingStatus"    => $site['pingStatus'] ?? "",
                        "messageTime"   => $site['lastMessageTime'] ?? null,
                              "dateUpdate"    => $site['dateUpdate'] ?? null,   // новое поле
                         "lastStatus"    => $site['lastStatus'] ?? null    // новое поле
                    ];
                }
            }
        }
    } catch (Exception $e) {
        error_log("Error when trying to get list of sites: " . $e->getMessage());
    }

    error_log("List of sites successfully retrieved.");

    // Возвращаем JSON
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($finalDataList, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
}
// Вызов функции
getStationsListLocal($region, $type);