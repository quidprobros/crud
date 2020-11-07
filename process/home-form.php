<?PHP // -*- mode:web -*-
die(Flight::has('sqlQueryResults'));
$pdo = (new SQLiteConnection())->connect();
if ($pdo != null)
    echo 'Connected to the SQLite database successfully!';
else
    echo 'Whoops, could not connect to the SQLite database!';

die();

$host = "127.0.0.1";
$user = 'someapp';
$pass = '';
$db   = 'subscriber_schema';
$charset = 'utf8mb4';
$port = '3306';

$results = [];

$dsn = "mysql:host={$host};charset={$charset};port={$port}";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

// create the database
try {
    $pdo = new \PDO($dsn, $user, $pass, $options);
    // check if database exists
    $sql = "SELECT SCHEMA_NAME
  FROM INFORMATION_SCHEMA.SCHEMATA
 WHERE SCHEMA_NAME = :db";

    $statement = $pdo->prepare($sql);
    $statement->bindParam(':db', $db);
    $statement->execute();
    $result = $statement->fetch(PDO::FETCH_ASSOC);
    if ($result['SCHEMA_NAME'] == $db) {
        $pdo->exec("use {$db}");
    } else {
        $sql_2 = "CREATE DATABASE IF NOT EXISTS {$db}";
        $statement_2 = $pdo->prepare($sql_2);
        $statement_2->execute();
        $result_2 = $statement_2->fetch(PDO::FETCH_ASSOC);
    }
} catch (PDOException  $e) {
    die($e->getMessage());
} catch (Exception $e) {
    die($e->getMessage());
}

// create table if necessary
try {
    $sql_3 = <<<EOF
    CREATE TABLE IF NOT EXISTS subscribers(
        subscriberName TINYTEXT NOT NULL,
        subscriberPhone TINYTEXT NOT NULL,
        subscriberEmail varchar(255) NOT NULL,
        subscriberMessage TEXT NOT NULL,
        subscriberIndex INT NOT NULL AUTO_INCREMENT,
        PRIMARY KEY (subscriberIndex),
        UNIQUE(subscriberEmail)
    ) ENGINE=INNODB;
EOF;

    $statement_3 = $pdo->prepare($sql_3);
    $statement_3->execute();
} catch (PDOException  $e) {
    die($e->getMessage());
} catch (Exception $e) {
    die($e->getMessage());
}


$subscriberName = filter_var($_POST['subscriberName'],
                             FILTER_SANITIZE_STRING);

$subscriberPhone = filter_var($_POST['subscriberPhone'],
                              FILTER_SANITIZE_NUMBER_INT);

$subscriberEmail = filter_var($_POST['subscriberEmail'],
                              FILTER_SANITIZE_EMAIL);

$subscriberMessage = filter_var($_POST['subscriberMessage'],
                                FILTER_SANITIZE_STRING);


// validate data
try {
    // validate phone number (USA)
    if(preg_match('/^[0-9]{10,11}$/', $subscriberPhone) !== 1) {
        throw new Exception("You've entered an invalid phone number. Only US phone numbers, with or without leading 1, are allowed.");
    }
    // validate email address
    $subscriberEmail = filter_var($subscriberEmail, FILTER_VALIDATE_EMAIL);

} catch (Exception $e) {
    die($e->getMessage());
}

$data = [
    "subscriberName" => $subscriberName,
    "subscriberPhone" => $subscriberPhone,
    "subscriberEmail" => $subscriberEmail,
    "subscriberMessage" => $subscriberMessage
];

$sql = 'INSERT INTO subscribers
  (subscriberName, subscriberPhone, subscriberEmail, subscriberMessage) VALUES
  (:subscriberName, :subscriberPhone, :subscriberEmail, :subscriberMessage)';

try {
    $statement = $pdo->prepare($sql);
    $statement->execute($data);
} catch (PDOException  $e) {
    if ($e->getCode() === "23000") {
        $results['message'] = "Thanks, {$subscriberName}, but you're already a subscriber.";
        die($results['message']);
    }
} catch (Exception $e) {
    die($e->getMessage());
} finally {
    $results['success'] = true;
    $results['message'] = "Thanks for subscribing, {$subscriberName}!";
    //    die($results['message']);
}


// get table results
try {
    $pdo->connection = null;
    $arrResult = $pdo->query('SELECT * FROM subscribers');
} catch (Exception $e) {
    die("bad query");

}

?>
<h1><?=$data['message']?></h1>
<table class="table">
    <thead>
        <tr>
            <th>Subscriber</th>
            <th>Email</th>
            <th>Telephone</th>
            <th>Message</th>
        </tr>
    </thead>
    <?PHP
    foreach ($arrResult as $row)
    {
    ?>
        <tr>
            <td><?=$row['subscriberName']?></td>
            <td><?=$row['subscriberEmail']?></td>
            <td><pre><?=$row['subscriberPhone']?></pre></td>
            <td><?=$row['subscriberMessage']?></td>
        </tr>
    <?PHP
    }
    ?>
</table>
