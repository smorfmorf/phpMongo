<?php




//! Конфиг 
$config = parse_ini_file(__DIR__ . '/db/config.ini', true);

//* PostgreSQL
$host_Postgres = $config['amurDb']['host'];
$dbName_Postgres = $config['amurDb']['dbname'];
$user_Postgres = $config['amurDb']['user'];
$pass_Postgres = $config['amurDb']['pass'];
$port_Postgres = $config['amurDb']['port'];





function getStationInstrumentListBySiteId($siteId)
{
    // Словарь siteInstrumentMeta (как в C#)
    $siteInstrumentMeta = [
        1 => "Порт и адрес подключения Telnet",
        4 => "IP адрес поста"
    ];

    //! Не работает модалка изза этого  (сюда не приходят данные)
    global $host_Postgres, $port_Postgres, $dbName_Postgres, $user_Postgres, $pass_Postgres;
    $dsn = sprintf(
        "pgsql:host=%s;port=%s;dbname=%s;user=%s;password=%s",
        $host_Postgres,
        $port_Postgres,
        $dbName_Postgres,
        $user_Postgres,
        $pass_Postgres
    );
    //!~ Это работает: 180 
    $dsnTest = "pgsql:host=10.8.3.180;port=5432;dbname=ferhri.amur;user=postgres;password=qq";

    $stationsData = [];
    try {
    // TODO: ТУТ 180 ПОРТ НУЖНО ЗАМЕНИТЬ dsnTest на dsn
        $pdo = new PDO($dsnTest);
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
$siteId = $_GET['siteId'] ?? '';

echo getStationInstrumentListBySiteId($siteId);