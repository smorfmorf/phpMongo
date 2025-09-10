<?php
header('Content-Type: application/json; charset=utf-8');

//! Конфиг 
$config = parse_ini_file(__DIR__ . '/db/config.ini', true);

// * PostgreSQL
$host_Postgres = $config['amurDb']['host'];
$dbName_Postgres = $config['amurDb']['dbname'];
$user_Postgres = $config['amurDb']['user'];
$pass_Postgres = $config['amurDb']['pass'];
$port_Postgres = $config['amurDb']['port'];



// --- Чтение параметров запроса ---
$siteId = $_GET['siteId'] ?? '';
echo getStationAttributeListBySiteId($siteId);




function getStationAttributeListBySiteId($siteId)
{

    global $host_Postgres, $port_Postgres, $dbName_Postgres, $user_Postgres, $pass_Postgres;
    $dsn = sprintf(
        "pgsql:host=%s;port=%s;dbname=%s;user=%s;password=%s",
        $host_Postgres,
        $port_Postgres,
        $dbName_Postgres,
        $user_Postgres,
        $pass_Postgres
    );

    $stationsData = [];

    try {
        $pdo = new PDO($dsn);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $sql = "
            SELECT 
                site_last_attr.entity_id AS entity_id, 
                site_attr_type.id AS attr_type_id,
                val_attr.value AS value,
                site_attr_type.name AS name
            FROM (
                SELECT 
                    entity_id,
                    attr_type_id,
                    MAX(date_s) AS max_date
                FROM meta.site_attr_value
                WHERE entity_id = :siteId
                GROUP BY entity_id, attr_type_id
            ) AS site_last_attr
            LEFT JOIN meta.site_attr_value AS val_attr 
                ON val_attr.date_s = site_last_attr.max_date
               AND val_attr.entity_id = site_last_attr.entity_id 
               AND val_attr.attr_type_id = site_last_attr.attr_type_id
            RIGHT JOIN meta.site_attr_type 
                ON site_last_attr.attr_type_id = site_attr_type.id
        ";

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':siteId', $siteId, PDO::PARAM_INT);
        $stmt->execute();

        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $stationsData[] = [
                "attrTypeId" => (int)$row['attr_type_id'],
                "desc"       => $row['name'],
                "value"      => $row['value'] === null ? "" : $row['value']
            ];
        }

        return json_encode($stationsData, JSON_UNESCAPED_UNICODE);
    } catch (Exception $ex) {
        return json_encode([
            "error" => "Произошла ошибка. Сообщение: " . $ex->getMessage()
        ], JSON_UNESCAPED_UNICODE);
    }
}