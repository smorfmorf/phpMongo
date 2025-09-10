<?php
// идём из db → .. (server) → vendor/autoload.php
require __DIR__ . '/../vendor/autoload.php';

use MongoDB\Client;

class Settings
{
    public $id;
    public $created;
    public $updated;
    public $name;
    public $description;
    public $value;

    public function __construct($data)
    {
        $this->id = $data['id'] ?? null;
        $this->created = $data['created'] ?? date('Y-m-d H:i:s');
        $this->updated = $data['updated'] ?? date('Y-m-d H:i:s');
        $this->name = $data['name'] ?? '';
        $this->description = $data['description'] ?? '';
        $this->value = $data['value'] ?? '';
    }

    public function toArray()
    {
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

class Message
{
    public $id;
    public $msg_key;
    public $rep_id;
    public $otg;
    public $realotg;
    public $nil;
    public $time;
    public $text;

    public function __construct($data)
    {
        $this->id = $data['id'] ?? null;
        $this->msg_key = $data['msg_key'] ?? '';
        $this->rep_id = $data['rep_id'] ?? '';
        $this->otg = $data['otg'] ?? '';
        $this->realotg = $data['realotg'] ?? '';
        $this->nil = $data['nil'] ?? '';
        $this->time = isset($data['time']) ? new DateTime($data['time']) : new DateTime();
        $this->text = $data['text'] ?? '';
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'msg_key' => $this->msg_key,
            'rep_id' => $this->rep_id,
            'otg' => $this->otg,
            'realotg' => $this->realotg,
            'nil' => $this->nil,
            'time' => new MongoDB\BSON\UTCDateTime($this->time->getTimestamp() * 1000),
            'text' => $this->text,
        ];
    }
}

class Site
{
    public int $id;
    public string $stationCode;
    public string $stationName;
    public string $pingStatus;
    public string $lastMessageTime;

    public function __construct(array $data)
    {
        $this->id = $data['id'];
        $this->stationCode = $data['stationCode'];
        $this->stationName = $data['stationName'];
        $this->pingStatus = $data['pingStatus'];
        $this->lastMessageTime = $data['lastMessageTime'];
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'stationCode' => $this->stationCode,
            'stationName' => $this->stationName,
            'pingStatus' => $this->pingStatus,
            'lastMessageTime' => $this->lastMessageTime,
        ];
    }
}

class TestSt
{
    public int $id;
    public string $stationCode;
    public string $stationName;
    public string $pingStatus;
    public string $lastMessageTime;
    public string $comments;

    public function __construct(array $data)
    {
        $this->id = $data['id'];
        $this->stationCode = $data['stationCode'];
        $this->stationName = $data['stationName'];
        $this->pingStatus = $data['pingStatus'];
        $this->lastMessageTime = $data['lastMessageTime'];
        $this->comments = $data['comments'];
    }

    public function toArray(): array
    {
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

class Database
{
    private static $client;
    private static $db;

    public static function connect()
    {
        $config = parse_ini_file(__DIR__.'./config.ini', true);
        self::$client = new Client($config['database']['host']);
        $dbName = $config['database']['dbname'];
        self::$db = self::$client->$dbName;
    }

    public static function getCollection($collectionName)
    {
        return self::$db->$collectionName;
    }


    // поверка существования коллекции (таблицы)
    public static function collectionExists($collectionName)
    {
        $collections = self::$db->listCollections();
        foreach ($collections as $collection) {
            if ($collection->getName() === $collectionName) {
                return true;
            }
        }
        return false;
    }


    // провекра есть ли бд 
    public static function databaseExists($dbName)
    {
        $databases = self::$client->listDatabases();
        foreach ($databases as $database) {
            if ($database->getName() === $dbName) {
                return true;
            }
        }
        return false;
    }


    // для импорта
    public static function importFromJson($jsonFile, $collectionName)
    {
        if (!file_exists($jsonFile)) {
            echo "Файл $jsonFile не найден!\n";
            return false;
        }
        
        $jsonData = file_get_contents($jsonFile);
        $data = json_decode($jsonData, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            echo "Ошибка парсинга JSON ($jsonFile): " . json_last_error_msg() . "\n";
            return false;
        }
        
        $collection = self::$db->$collectionName;
        
        if (count($data) > 0) {
            $collection->insertMany($data);
            echo "Данные из $jsonFile успешно импортированы в коллекцию $collectionName (" . count($data) . " записей)\n";
        }
        
        return true;
    }
}

// Подключаемся к MongoDB
Database::connect();

// Проверяем существование базы данных
$config = parse_ini_file(__DIR__ . './config.ini', true);
$dbName = $config['database']['dbname'];

// Маппинг JSON файлов к коллекциям
$collectionsMap = [
    __DIR__.'/messages.json' => 'messages',
    __DIR__.'/settings.json' => 'settings',
    __DIR__.'/sites.json' => 'sites',
    __DIR__.'/testSt.json' => 'testSt'
];

if (!Database::databaseExists($dbName)) {
    echo "База данных $dbName не существует. Создаем и инициализируем...\n";
    
    // Импортируем данные из всех JSON файлов
    foreach ($collectionsMap as $jsonFile => $collectionName) {
        if (file_exists($jsonFile)) {
            Database::importFromJson($jsonFile, $collectionName);
        } else {
            echo "Файл $jsonFile не найден, коллекция $collectionName будет пустой\n";
            // Создаем пустую коллекцию
            $collection = Database::getCollection($collectionName);
        }
    }
    
    echo "База данных успешно инициализирована!\n";
} else {
    echo "База данных $dbName уже существует.\n";
    
    // Проверяем и заполняем каждую коллекцию
    foreach ($collectionsMap as $jsonFile => $collectionName) {
        if (Database::collectionExists($collectionName)) {
            $collection = Database::getCollection($collectionName);
            $count = $collection->countDocuments();
            
            if ($count === 0) {
                echo "Коллекция $collectionName пустая. ";
                if (file_exists($jsonFile)) {
                    echo "Загружаем данные из $jsonFile...\n";
                    Database::importFromJson($jsonFile, $collectionName);
                } else {
                    echo "Файл $jsonFile не найден, коллекция останется пустой\n";
                }
            } else {
                echo "Коллекция $collectionName уже содержит $count записей.\n";
            }
        } else {
            echo "Коллекция $collectionName не существует. Создаем и ";
            if (file_exists($jsonFile)) {
                echo "загружаем данные из $jsonFile...\n";
                Database::importFromJson($jsonFile, $collectionName);
            } else {
                echo "создаем пустую коллекцию (файл $jsonFile не найден)...\n";
                $collection = Database::getCollection($collectionName);
            }
        }
    }
}

echo "Готово! Проверка и инициализация завершены.\n";