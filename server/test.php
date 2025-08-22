<?php
require 'vendor/autoload.php'; 
use MongoDB\Client;


class Settings {
    public $id;
    public $created;
    public $updated;
    public $name;
    public $description;
    public $value;

    public function __construct($data) {
        $this->id = $data['id'] ?? null;
        $this->created = $data['created'] ?? date('Y-m-d H:i:s');
        $this->updated = $data['updated'] ?? date('Y-m-d H:i:s');
        $this->name = $data['name'] ?? '';
        $this->description = $data['description'] ?? '';
        $this->value = $data['value'] ?? '';
    }


    // конвертируем объект в массив
    public function toArray() {
        return [
            'id' => $this->id,
            'created' => $this->created,
            'updated' => $this->updated,
            'name' => $this->name,
            'description' => $this->description,
            'value' => $this->value
        ];
    }
}

class Message {
    public $id;
    public $msg_key;
    public $rep_id;
    public $otg;
    public $realotg;
    public $nil;
    public $time;
    public $text;

    public function __construct($data) {
        $this->id = $data['id'] ?? null;
        $this->msg_key = $data['msg_key'] ?? '';
        $this->rep_id = $data['rep_id'] ?? '';
        $this->otg = $data['otg'] ?? '';
        $this->realotg = $data['realotg'] ?? '';
        $this->nil = $data['nil'] ?? '';
        // Время в PHP обычно в формате DateTime, но для Mongo можно использовать ISODate или строку:
        $this->time = isset($data['time']) ? new DateTime($data['time']) : new DateTime();
        $this->text = $data['text'] ?? '';
    }

    public function toArray() {
        return [
            'id' => $this->id,
            'msg_key' => $this->msg_key,
            'rep_id' => $this->rep_id,
            'otg' => $this->otg,
            'realotg' => $this->realotg,
            'nil' => $this->nil,
            // MongoDB поддерживает даты в формате UTCDateTime, можно конвертировать:
            'time' => new MongoDB\BSON\UTCDateTime($this->time->getTimestamp()*1000),
            'text' => $this->text,
        ];
    }
}

class Station{
    public int $id;
    public string $code;
    public string $name;
    public array $hours = [];
    public array $noVisualHours = [];
    public string $loc;
    public string $header;

    public function __construct(array $data)
    {
        $this->id = $data['id'];
        $this->code = $data['code'];
        $this->name = $data['name'];
        $this->hours = $data['hours'] ?? [];
        $this->noVisualHours = $data['noVisualHours'] ?? [];
        $this->loc = $data['loc'];
        $this->header = $data['header'];
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'name' => $this->name,
            'hours' => $this->hours,
            'noVisualHours' => $this->noVisualHours,
            'loc' => $this->loc,
            'header' => $this->header,
        ];
    }
}

class Site {
    public int $id;
    public string $stationCode;
    public string $stationName;
    public string $pingStatus;
    public string $lastMessageTime;

    public function __construct(array $data) {
        $this->id = $data['id'];
        $this->stationCode = $data['stationCode'];
        $this->stationName = $data['stationName'];
        $this->pingStatus = $data['pingStatus'];
        $this->lastMessageTime = $data['lastMessageTime'];
    }

    public function toArray(): array {
        return [
            'id' => $this->id,
            'stationCode' => $this->stationCode,
            'stationName' => $this->stationName,
            'pingStatus' => $this->pingStatus,
            'lastMessageTime' => $this->lastMessageTime,
        ];
    }
}

class EntityGroup {
    public int $id;
    public string $name;
    public string $entityTabName;
    public string $entityTabNameRus;

    public function __construct(array $data) {
        $this->id = $data['id'];
        $this->name = $data['name'];
        $this->entityTabName = $data['entityTabName'];
        $this->entityTabNameRus = $data['entityTabNameRus'];
    }

    public function toArray(): array {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'entityTabName' => $this->entityTabName,
            'entityTabNameRus' => $this->entityTabNameRus,
        ];
    }
}

class EntityGroupXSite {
    public int $id;
    public array $entityGroupId;
    public int $siteId;

    public function __construct(array $data) {
        $this->id = $data['id'];
        $this->entityGroupId = $data['entityGroupId'];
        $this->siteId = $data['SiteId'];
    }

    public function toArray(): array {
        return [
            'id' => $this->id,
            'entityGroupId' => $this->entityGroupId,
            'siteId' => $this->siteId,
        ];
    }
}

class TestSt {
    public int $id;
    public string $stationCode;
    public string $stationName;
    public string $pingStatus;
    public string $lastMessageTime;
    public string $comments;

    public function __construct(array $data) {
        $this->id = $data['id'];
        $this->stationCode = $data['stationCode'];
        $this->stationName = $data['stationName'];
        $this->pingStatus = $data['pingStatus'];
        $this->lastMessageTime = $data['lastMessageTime'];
        $this->comments = $data['comments'];
    }

    public function toArray(): array {
        return [
            'id' => $this->id,
            'stationCode' => $this->stationCode,
            'stationName' => $this->stationName,
            'pingStatus' => $this->pingStatus,
            'lastMessageTime' => $this->lastMessageTime,
            'comments' => $this->comments,
        ];
    }
}

class MessageMF {
    public int $id;
    public string $date_utc;
    public string $tg_title;
    public string $message;
    public string $name;
    public string $st_title;
    public string $abonent;

    // Особенность — в конструкторе есть обработка поля message, где строка делится по title и заменяется символ с кодом 0x03 на пробел.
    public function __construct(array $data) {
        $this->id = $data['id'];
        $this->date_utc = $data['date_utc'];
        $this->tg_title = $data['tg_title'];

        // Разбиваем message по заголовку и берем вторую часть
        $parts = explode($this->tg_title, $data['message'], 2);
        $msgPart = $parts[1] ?? '';
        
        $char = chr(hexdec('03'));// получам символ с кодом 0x03 
        $this->message = str_replace($char, ' ', $msgPart);
        $this->name = $data['name'];
        $this->st_title = $data['st_title'];
        $this->abonent = $data['abonent'];
    }

    public function toArray(): array {
        return [
            'id' => $this->id,
            'date_utc' => $this->date_utc,
            'tg_title' => $this->tg_title,
            'message' => $this->message,
            'name' => $this->name,
            'st_title' => $this->st_title,
            'abonent' => $this->abonent,
        ];
    }
}


class Database {
    private static $client;
    private static $db;

    // Подключаемся к БД и сохраняем подключение в $client и выбираем базу
    public static function connect($dbName = 'sdt') {
        self::$client = new Client("mongodb://127.0.0.1:27017");
        self::$db = self::$client->$dbName;
    }
    // получить коллекцию по названию 
    public static function getCollection($collectionName) {
        return self::$db->$collectionName;
    }
}

// подключаемся к MongoDB
Database::connect("sdt"); 


//! -------------------------------------------------------------------------------------------------------------------------------
//! Получаем нужную коллекцию
// $stationCollection = Database::getCollection('stations');
// $messageCollection = Database::getCollection('messages');
// $settingsCollection = Database::getCollection('settings');
// $collection = Database::getCollection('sites');
// $collection = Database::getCollection('entityGroups');
// $collection = Database::getCollection('entityGroupXSite');
// $collection = Database::getCollection('testSt');

// $message = new MessageMF($messageData);
// $collection = Database::getCollection('messages'); 
//! и потом фигачим туда куда нам нужно $stationCollection->insertOne($station->toArray());

//*1) Запись Station:
// $station = new Station([
//     'id' => 1,
//     'code' => 'A01',
//     'name' => 'Main Station',
//     'hours' => [1, 2, 3],
//     'noVisualHours' => [4, 5],
//     'loc' => '51.5074, 0.1278',
//     'header' => 'Main Header'
// ]);

// $stationCollection = Database::getCollection('stations');
// $stationCollection->insertOne($station->toArray());


//* 2) Запись Message
// $msg = new Message([
//     'id' => 1,
//     'msg_key' => 'key123',
//     'rep_id' => 'rep456',
//     'otg' => 'отг',
//     'realotg' => 'реальный отг',
//     'nil' => '',
//     'time' => '2025-08-22 12:34:56',
//     'text' => 'Пример текста сообщения',
// ]);

// $collection = Database::getCollection('message');
// $collection->insertOne($msg->toArray());


//* 3) Запись Settings
// $setting = new Settings([
//     'id' => 1,
//     'name' => 'example_setting',
//     'description' => 'Описание настройки',
//     'value' => 'значение'
// ]);

// $collection = Database::getCollection('settings');
// $collection->insertOne($setting->toArray());


//* 4) Запись Site
// $site = new Site([
//     'id' => 1,
//     'stationCode' => 'A01',
//     'stationName' => 'Main Station',
//     'pingStatus' => 'OK',
//     'lastMessageTime' => date('Y-m-d H:i:s')
// ]);

// $collection = Database::getCollection('sites');
// $collection->insertOne($site->toArray());


//* 5) Запись EntityGroup
// $entityGroup = new EntityGroup([
//     'id' => 1,
//     'name' => 'GroupName',
//     'entityTabName' => 'TabName',
//     'entityTabNameRus' => 'ТаблицаРус'
// ]);

// $collection = Database::getCollection('entityGroups');
// $collection->insertOne($entityGroup->toArray());

//* 6) Запись EntityGroupXSite
// $entityGroupXSite = new EntityGroupXSite([
//     'id' => 1,
//     'entityGroupId' => [1, 2, 3],
//     'SiteId' => 5
// ]);

// $collection = Database::getCollection('entityGroupXSite');
// $collection->insertOne($entityGroupXSite->toArray());

//* 7) Запись TestSt
// $testSt = new TestSt([
//     'id' => 1,
//     'stationCode' => 'A01',
//     'stationName' => 'Test Station',
//     'pingStatus' => 'OK',
//     'lastMessageTime' => date('Y-m-d H:i:s'),
//     'comments' => 'Some comment here'
// ]);

// $collection = Database::getCollection('testSt');
// $collection->insertOne($testSt->toArray());

//* 8) Запись MessageMF
// $messageData = [
//     'id' => 1,
//     'date_utc' => '2025-08-22 10:00:00',
//     'tg_title' => 'Alert',
//     'message' => 'AlertThis is the actual message' . chr(3) . ' with control char',
//     'name' => 'John Doe',
//     'st_title' => 'Station X',
//     'abonent' => 'User123'
// ];

// $message = new MessageMF($messageData);
// $collection = Database::getCollection('messages');
// $collection->insertOne($message->toArray());
//! -------------------------------------------------------------------------------------------------------------------------------







// получаем коллекцию
$collection = Database::getCollection('Lolipap');
// делаем операцию
$collection->insertOne(['name' => 'tes2t']);
$res = $collection->find()->toArray();
echo json_encode($res);




// создаём новую запись
$setting = new Settings([
    'id' => 1,
    'name' => 'example_setting',
    'description' => 'Описание настройки',
    'value' => 'значение'
]);

// вставляем в Mongo
$collection->insertOne($setting->toArray());


$station = new Station([
    'id' => 1,
    'code' => 'A123',
    'name' => 'Main Station',
    'hours' => [8, 12, 18],
    'noVisualHours' => [2, 4],
    'loc' => 'N45.0 E90.0',
    'header' => 'Station Header Info'
]);
$collection->insertOne($station->toArray());

// Создание объекта Site
$site = new Site([
    'id' => 1,
    'stationCode' => 'A01',
    'stationName' => 'Main Stationddddd213132',
    'pingStatus' => 'OK',
    'lastMessageTime' => date('Y-m-d H:i:s')
]);
//драйвер монго пытается преобразовать этот объект в массив - BSON-документ (формат хранения в MongoDB).
$collection -> insertOne($site);
// То есть, он по сути делает нечто вроде:
// $collection->insertOne([
//     'id' => $site->id,
//     'stationCode' => $site->stationCode,
//     ...
// ]);




echo "Данные вставлены!";