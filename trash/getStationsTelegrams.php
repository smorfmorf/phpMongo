<?php


header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// Получаем параметр из запроса
$stationCode = isset($_GET['stationCode']) ? $_GET['stationCode'] : '';

if (empty($stationCode)) {
    http_response_code(400);
    echo json_encode(array('error' => 'Station code is required'));
    exit;
}

function getStationTelegrams($stationCode) {
    $dbHost = "10.8.3.182";
    $dbName = "meteo"; 
    $dbUser = "habwrf";
    $dbPass = "10model@@";
    
    $result = array();
    
    try {
        $mysqli = new mysqli($dbHost, $dbUser, $dbPass, $dbName);
        
        if ($mysqli->connect_error) {
            throw new Exception("Connection failed: " . $mysqli->connect_error);
        }
        
        // $tableName = "rpa_rep_" . date('dmy');
        $tableName = "rpa_rep_290725";

        $stationCode = $mysqli->real_escape_string($stationCode);
        
        $query = "SELECT * FROM $tableName 
                 WHERE nil = 'DAT' 
                 AND rep_id = '$stationCode'";
        
        if ($res = $mysqli->query($query)) {
            while ($row = $res->fetch_row()) {
                $result[] = array(
                    'msg_key' => $row[2],
                    'nil' => $row[8],
                    'time' => $row[13],
                    'text' => $row[16]
                );
            }
            $res->free();
        }
        
        $mysqli->close();
        
    } catch (Exception $e) {
        return array('error' => $e->getMessage());
    }
    
    return $result;
}



$telegrams = getStationTelegrams($stationCode);
echo json_encode($telegrams, JSON_PRETTY_PRINT);
?>