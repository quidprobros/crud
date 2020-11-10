<?PHP // -*- mode: php; -*-

define("PROJECT_ROOT", dirname(__DIR__));

require_once PROJECT_ROOT . '/vendor/autoload.php';

use \Monolog\Logger;
use \Monolog\Handler\BrowserConsoleHandler;
use \Monolog\Handler\StreamHandler;


use \Riimu\Kit\PathJoin\Path;

$whoops = new \Whoops\Run;
$whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
$whoops->register();

// Kint
Kint\Renderer\RichRenderer::$folder = false;
function ddd(...$parameters)
{
    d(...$parameters);
    //    exit;
}


Kint::$aliases[] = 'ddd';


define("VIEWS_ROOT", PROJECT_ROOT . "/views");

Flight::set('flight.views.extension', '.tpl');
Flight::set('flight.views.path', VIEWS_ROOT);


$logger = new \Monolog\Logger('general');

Flight::register("pathway", "Riimu\Kit\PathJoin\Path");
Flight::register("lipsum", "joshtronic\LoremIpsum");

// $p = new App\SQLiteConnection();

// $p->connect();
// The callback will be passed the object that was constructed

Flight::register(
    'db',
    'PDO',
    array('sqlite:'. App\Config::PATH_TO_SQLITE_FILE, '', ''),
    function ($db) {
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    }
);


Kint::$enabled_mode = true;
error_reporting(E_ALL);
ini_set('display_errors', 'On');
Flight::set('flight.log_errors', true);

Flight::set('flight.base_url', '..');

$loggerPath = Path::join(dirname(__DIR__), 'logs/local.debug.log');

$handler = new StreamHandler($loggerPath, Logger::DEBUG);
$logger->pushHandler($handler);

$logger->pushProcessor(function (array $record) {
    $record['extra']['hostname'] = gethostname();
    return $record;
});

$logger->info("APP_ENVIRONMENT: ".getenv('APP_ENVIRONMENT'));
$logger->info("Logger is ready.");


Flight::route('GET /(home|index)', function () {
    Flight::render('index', []);
});

Flight::route('GET /info', function () {
    Flight::render('info', []);
});


Flight::route('POST /process', function () use ($logger) {
    $postData = (array) Flight::request()->data;
    error_log(print_r($postData,true));
    try {
        $x = new App\SQLiteConnection();
    
    } catch (\Exception $e) {
        s($e);
    }

    exit;

    // $postData = (array) Flight::request()->data;
    // $processor = new App\FormProcessor($postData);
    // return Flight::json(["arr"=> 333]);
    
});

// Flight::route('POST /process/response', function () {
//     Flight::render('process/response', []);
// });

// Flight::route('GET /tests/@test', function ($test) {
//     Flight::render("tests/".$test, []);
// });

// Flight::route('GET /css/@style', function ($style) {
//     error_log('lol');
//     Flight::log($style);
//     ddd($style);
//     // require_once($install_path . '/public/css/bootstrap.min.css' );
//     //require_once(string $file_name )
//     // d(Flight::request());
//     // d($route);
//     die();
// });

Flight::map('error', function ($ex) use ($whoops) {
        $whoops->handleException($ex);
    });

Flight::map('notFound', function () {
        Flight::stop(404);
        Flight::render('404', []);
});



// // Flight::map('warn', function ($ex) {
// //     Flight::log($ex->getMessage());
// //     return ;
// // });


Flight::start();
