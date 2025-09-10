<?php
require 'vendor/autoload.php'; // если будут нужны сторонние библиотеки, например для MongoDB

/**
 * Получение строки подключения к базе SDT (MySQL)
 */
function getSDTConnection() {
    $config = [
        'SDTDbAddr' => '10.8.3.182',
        'SDTDbUser' => 'habwrf',
        'SDTDbPass' => '10model@@',
        'SDTDb'     => 'meteo'
    ];
    return $config;
}

/**
 * Получение строки подключения к базе Amur (PostgreSQL)
 */
function getAmurConnection() {
    $config = [
        'amurDbAddr' => '10.8.3.180',
        'amurDbUser' => 'postgres',
        'amurDbPass' => 'qq',
        'amurDb'     => 'ferhri.amur'
    ];

    return sprintf(
        "pgsql:host=%s;port=5432;dbname=%s;user=%s;password=%s",
        $config['amurDbAddr'],
        $config['amurDb'],
        $config['amurDbUser'],
        $config['amurDbPass']
    );
}




/**
 * Основная функция получения последних телеграмм станции
 */
function getLastTelegramsForStation($stationCode, $siteId) {
    $resRep = [];
    $resBul = [];
    $filters = "";

    try {
        // --- PostgreSQL: получаем фильтры ---
        $pdo = new PDO(getAmurConnection(), null, null, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
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
        $cfg = getSDTConnection();
        $dsn = "mysql:host={$cfg['SDTDbAddr']};dbname={$cfg['SDTDb']};charset=utf8";
        $pdoMy = new PDO($dsn, $cfg['SDTDbUser'], $cfg['SDTDbPass'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

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
$stationCode = $_GET['stationCode'] ?? '';
$siteId      = $_GET['siteId'] ?? '';

if ($stationCode !== '' && $siteId !== '') {
    getLastTelegramsForStation($stationCode, $siteId);
} else {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['error' => 'Не переданы параметры stationCode или siteId'], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
}