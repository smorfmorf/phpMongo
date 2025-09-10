<?php



//! Конфиг 
$config = parse_ini_file(__DIR__ . '/db/config.ini', true);

//* PostgreSQL
$host_Postgres = $config['amurDb']['host'];
$dbName_Postgres = $config['amurDb']['dbname'];
$user_Postgres = $config['amurDb']['user'];
$pass_Postgres = $config['amurDb']['pass'];
$port_Postgres = $config['amurDb']['port'];



function setStationInstrumentData($siteId, $values, $instrumentIds)
{
    // Словарь siteInstrumentMeta (как в C#)
    $siteInstrumentMeta = [
        1 => "Порт и адрес подключения Telnet",
        4 => "IP адрес поста"
    ];

    // Строка подключения
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

        $now = date("Y-m-d");
        $preparedValues = [];
        $params = [];

        // Формируем список VALUES с параметрами
        for ($i = 0; $i < count($values); $i++) {
            $preparedValues[] = "(:siteId{$i}, :instrumentId{$i}, :dateS{$i}, :dateF{$i}, :locDesc{$i}, :value{$i})";

            $params[":siteId{$i}"]      = $siteId;
            $params[":instrumentId{$i}"] = $instrumentIds[$i];
            $params[":dateS{$i}"]      = $now;
            $params[":dateF{$i}"]      = $now;
            $params[":locDesc{$i}"]    = $siteInstrumentMeta[$instrumentIds[$i]] ?? "";
            $params[":value{$i}"]      = $values[$i];
        }

        $sql = "
            INSERT INTO meta.site_instrument 
                (site_id, instrument_id, date_s, date_f, location_description, description) 
            VALUES " . implode(", ", $preparedValues) . "
            ON CONFLICT (site_id, instrument_id, location_description) 
            DO UPDATE SET 
                (date_f, description) = (EXCLUDED.date_f, EXCLUDED.description)
        ";

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
// $siteId = 123;
// $values = ["значение6666"];
// $instrumentIds = [4];

$siteId = $_GET['siteId'] ?? "";
$values = $_GET['values'] ?? "";
$instrumentIds = $_GET['instrumentIds'] ?? "";

echo setStationInstrumentData($siteId, $values, $instrumentIds);