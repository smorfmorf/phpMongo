<?php



//! Конфиг 
$config = parse_ini_file(__DIR__ . '/db/config.ini', true);

//* PostgreSQL
$host_Postgres = $config['amurDb']['host'];
$dbName_Postgres = $config['amurDb']['dbname'];
$user_Postgres = $config['amurDb']['user'];
$pass_Postgres = $config['amurDb']['pass'];
$port_Postgres = $config['amurDb']['port'];




function setStationAttributeData($siteId, $attrValues, $attrIds)
{
    // строка подключения к PostgreSQL
    // $dsn = "pgsql:host=10.8.3.219;port=5433;dbname=ferhri.amur;user=postgres;password=qq";
  global $host_Postgres, $port_Postgres, $dbName_Postgres, $user_Postgres, $pass_Postgres;

    $dsn = sprintf(
        "pgsql:host=%s;port=%s;dbname=%s;user=%s;password=%s",
        $host_Postgres,
        $port_Postgres,
        $dbName_Postgres,
        $user_Postgres,
        $pass_Postgres
    );

    try {
        $pdo = new PDO($dsn);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $now = date("Y-m-d H:i:s");
        $preparedValues = [];
        $params = [];

        // формируем VALUES с параметрами
        for ($i = 0; $i < count($attrValues); $i++) {
            $preparedValues[] = "(:siteId{$i}, :attrId{$i}, :attrValue{$i}, :dateS{$i})";

            $params[":siteId{$i}"]    = $siteId;
            $params[":attrId{$i}"]    = $attrIds[$i];
            $params[":attrValue{$i}"] = $attrValues[$i];
            $params[":dateS{$i}"]     = $now;
        }

        $sql = "INSERT INTO meta.site_attr_value (entity_id, attr_type_id, value, date_s) VALUES "
             . implode(", ", $preparedValues);

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        return json_encode("Операция успешно выполнена.", JSON_UNESCAPED_UNICODE);
    } catch (Exception $ex) {
        return json_encode([
            "error" => "Произошла ошибка. Сообщение: " . $ex->getMessage()
        ], JSON_UNESCAPED_UNICODE);
    }
}



header('Content-Type: application/json; charset=utf-8');

$siteId = $_GET['siteId'] ?? '';
$attrValues = $_GET['attrValues'] ?? '';
$attrIds = $_GET['attrIds'] ?? '';


echo setStationAttributeData($siteId, $attrValues, $attrIds);