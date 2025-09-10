<?php
require __DIR__ . '/vendor/autoload.php';
header('Content-Type: application/json; charset=utf-8');

$data = json_decode(file_get_contents('php://input'), true);

if (!$data || !isset($data['siteId'], $data['lastStatus'], $data['dateUpdate'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Некорректные данные']);
    exit;
}

try {
    $mongo = new MongoDB\Client("mongodb://localhost:27017");
    $db = $mongo->selectDatabase("sdt");
    $sitesCol = $db->selectCollection("sites");

    // Обновляем запись по siteId
    $updateResult = $sitesCol->updateOne(
        ['_id' => (int)$data['siteId']],
        ['$set' => [
            'lastStatus' => $data['lastStatus'],
            'dateUpdate' => $data['dateUpdate']
        ]]
    );

    echo json_encode(['status' => 'success', 'modifiedCount' => $updateResult->getModifiedCount()]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}