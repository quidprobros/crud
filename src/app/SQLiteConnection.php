<?PHP
namespace App;

class SQLiteConnection {
    public function __construct() {
        if (null == \Flight::db()) {
            throw new \Exception("DB instance not found!");
        }

        try {
        $statement = <<<EOF
    CREATE TABLE IF NOT EXISTS subscribers(
        subscriberIndex INTEGER PRIMARY KEY,
        subscriberName TEXT NOT NULL,
        subscriberPhone TEXT NOT NULL UNIQUE,
        subscriberEmail TEXT NOT NULL UNIQUE,
        subscriberMessage TEXT NOT NULL
    );
EOF;
        $pstatement = \Flight::db()->prepare($statement);
        $pstatement->execute();
        } catch (\Exception $e) {
            die($e->getMessage());
        }
    }

    public function add($data) {
            
    }

}
