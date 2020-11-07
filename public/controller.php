<?PHP // -*- mode: php; -*-
$script_path = realpath(basename(getenv("SCRIPT_NAME")));
$slash = explode('/', getenv("SCRIPT_NAME"));
$current_filename = $slash[count($slash) - 1];
$install_path = str_replace($current_filename, '', $script_path); // Fixing the host URL.
$user_path = dirname($install_path)."/";
$host_url = str_replace($current_filename, '', getenv("SCRIPT_NAME"));

define("PROJECT_ROOT", dirname(__DIR__));

require_once PROJECT_ROOT . '/vendor/autoload.php';

use \Monolog\Logger;
use \Monolog\Handler\BrowserConsoleHandler;
use \Monolog\Handler\StreamHandler;

use \Riimu\Kit\PathJoin\Path;

use Snipe\BanBuilder\CensorWords;

$censor = new CensorWords;

Flight::map('censorThis', function($censored_text) use ($censor) {
    return ($censor->censorString($censored_text))['clean'];
});

$whoops = new \Whoops\Run;
$whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
$whoops->register();

// Kint
Kint\Renderer\RichRenderer::$folder = false;
function ddd(...$parameters)
{
    d(...$parameters);
    exit;
}


Kint::$aliases[] = 'ddd';

define("VIEWS_ROOT", PROJECT_ROOT . "/views");

Flight::set('flight.views.extension', '.tpl');
Flight::set('flight.views.path', VIEWS_ROOT);


$logger = new \Monolog\Logger('general');


Flight::register("pathway", "Riimu\Kit\PathJoin\Path");
Flight::register("lipsum", "joshtronic\LoremIpsum");


$host = 'mysql';
$user = getenv('MYSQL_ROOT_USER');
$pass = getenv('MYSQL_ROOT_PASSWORD');
$db   = 'subscriber_schema';
$charset = 'utf8mb4';
$port = '3306';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset;port=$port";

$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => true,
];

Flight::register('db', 'PDO', [$dsn, $user, $pass, $options]);

Kint::$enabled_mode = true;
error_reporting(E_ALL);
ini_set('display_errors', 'On');
Flight::set('flight.log_errors', true);
Flight::set('flight.base_url', '..');
$logger->pushHandler(new BrowserConsoleHandler(\Monolog\Logger::DEBUG));
$loggerPath = Path::join(dirname(__DIR__), 'logs/local.debug.log');

$handler = new StreamHandler($loggerPath, Logger::DEBUG);
$logger->pushHandler($handler);

$logger->pushProcessor(function (array $record) {
    $record['extra']['hostname'] = gethostname();
    return $record;
});

$logger->info("APP_ENVIRONMENT: ".getenv('APP_ENVIRONMENT'));
$logger->info("Logger is ready.");

Flight::map("log", function ($msg) use ($logger) {
    return $logger->info($msg);
});

Flight::route('GET /(home|index)', function () {
    Flight::render('index', []);
    Flight::log("home page");
});

Flight::route('GET /info', function () {
    Flight::render('info', []);
    Flight::log("php info");
});

Flight::route('POST /process/home-form', function () {
    try {
        Flight::log("making db connection ...");
        $pdo = Flight::db();
    } catch (\PDOException $e) {
        Flight::error($e);
    }
    $request = Flight::request();
    $data = $request->data;
    Flight::render('process/home-form', ['pdo' =>  $pdo]);
    Flight::log("POST");
});

// Flight::route('POST /process/response', function () {
//     Flight::render('process/response', []);
// });

Flight::route('GET /tests/@test', function ($test) {
    Flight::render("tests/".$test, []);
});

d(Flight::request());

Flight::route('GET /css/@style', function ($style) use ($install_path) {
    ddd($style);
    // require_once($install_path . '/public/css/bootstrap.min.css' );
    //require_once(string $file_name )
    // d(Flight::request());
    // d($route);
    die();
});

Flight::route('GET *', function () {
    die("damn");
});

Flight::map('error', function ($ex) use ($whoops) {
    $whoops->handleException($ex);
});

// Flight::map('warn', function ($ex) {
//     Flight::log($ex->getMessage());
//     return ;
// });

Flight::map('notFound', function () {
    Flight::stop(404);
    Flight::render('404', []);
});

Flight::start();
