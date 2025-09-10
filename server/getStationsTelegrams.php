<?php
require 'vendor/autoload.php'; // если будут нужны сторонние библиотеки, например для MongoDB


//! Конфиг 
$config = parse_ini_file(__DIR__ . '/db/config.ini', true);

//* PostgreSQL
$host_Postgres = $config['amurDb']['host'];
$dbName_Postgres = $config['amurDb']['dbname'];
$user_Postgres = $config['amurDb']['user'];
$pass_Postgres = $config['amurDb']['pass'];
$port_Postgres = $config['amurDb']['port'];

//* MySQL
$host_MySQL = $config['SDTDb']['host'];
$dbName_MySQL = $config['SDTDb']['dbname'];
$user_MySQL = $config['SDTDb']['user'];
$pass_MySQL = $config['SDTDb']['pass'];
//  $dsnPg = "pgsql:host=10.8.3.219;port=5433;dbname=ferhri.amur;user=postgres;password=qq";




/**
 * Основная функция получения последних телеграмм станции
 */
function getLastTelegramsForStation($stationCode, $siteId) {
    $resRep = [];
    $resBul = [];
    $filters = "";

    try {
        // --- PostgreSQL: получаем фильтры ---
       global $host_Postgres, $port_Postgres, $dbName_Postgres, $user_Postgres, $pass_Postgres;
        $dsnPg = sprintf(
            "pgsql:host=%s;port=%s;dbname=%s;user=%s;password=%s",
            $host_Postgres,
            $port_Postgres,
            $dbName_Postgres,
            $user_Postgres,
            $pass_Postgres
        );

        $pdo = new PDO($dsnPg);
        $sql = "SELECT value FROM meta.site_attr_value WHERE entity_id = :siteId AND attr_type_id = 1030";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['siteId' => $siteId]);
        $temp = $stmt->fetchColumn();

        if ($temp !== false && $temp !== "") {
            $tempArr = explode(',', $temp);
            $filtersArr = [];
            foreach ($tempArr as $f) {
                $f = trim($f);
                if ($f !== "") {
                    $filtersArr[] = "UPPER(msg_key) LIKE '%" . strtoupper($f) . "%'";
                }
            }
            if (!empty($filtersArr)) {
                $filters = " AND (" . implode(" OR ", $filtersArr) . ")";
            }
        }

        // --- MySQL: получаем телеграммы ---
      
        global $host_MySQL, $dbName_MySQL, $user_MySQL, $pass_MySQL;
        $dsnMySQL = sprintf(
            "mysql:host=%s;dbname=%s;charset=utf8;user=%s;password=%s",
            $host_MySQL,
            $dbName_MySQL,
            $user_MySQL,
            $pass_MySQL
        );
        
        $pdoMy = new PDO($dsnMySQL);

        $tableName = "rpa_rep_290725"; // подставьте актуальное имя таблицы
        $sqlMy = "SELECT * FROM $tableName WHERE nil='DAT' AND rep_id=:stationCode $filters";
        $stmtMy = $pdoMy->prepare($sqlMy);
        $stmtMy->execute(['stationCode' => $stationCode]);

        while ($row = $stmtMy->fetch(PDO::FETCH_ASSOC)) {
            $resRep[] = [
                'msg_key' => $row['msg_key'] ?? null,
                'nil'     => $row['nil'] ?? null,
                'time'    => isset($row['time']) ? gmdate("Y-m-d\TH:i:s\Z", strtotime($row['time'])) : null,
                'text'    => $row['text'] ?? null
            ];
        }

    } catch (Exception $e) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['error' => "Произошла ошибка: " . $e->getMessage()], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    // --- Возврат JSON ---
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        'rep' => $resRep,
        'bul' => $resBul
    ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
}

// --- Чтение параметров запроса ---
$stationCode = $_GET['stationCode'] ?? '31152';
$siteId      = $_GET['siteId'] ?? '234';

if ($stationCode !== '' && $siteId !== '') {
    getLastTelegramsForStation($stationCode, $siteId);
} else {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['error' => 'Не переданы параметры stationCode или siteId'], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
}