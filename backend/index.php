<?php
// import Composer's autoloader.
require_once __DIR__ . '/vendor/autoload.php';

// loading the autoloader lets PHP understand dependencies (like Dotenv) without you having to require-once-ing the dependencies' source files
// Dotenv is a parser for .env
use Dotenv\Dotenv;
use Jenniferd\NutritionApi\Database;
use Jenniferd\NutritionApi\Router;

$dotenv = Dotenv::createImmutable(__DIR__);// finds .env in this directory
$dotenv->load(); // ...and load its values

$database = new Database();
$db = $database->getConnection();

// we have to handle both HTTP method and requested resource/route
$method = $_SERVER['REQUEST_METHOD'];
$requestedUrl = $_SERVER['REQUEST_URI'];
// you want just the path, not the query. so you don't want the full url.
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

$router = new Router();

$router->get('/log', function() use ($db) {
    $stmt = $db->query('SELECT * FROM consumption_log');
    $log = $stmt->fetchAll(PDO::FETCH_ASSOC);
    header('Content-Type: application/json');
    echo json_encode($log);
});

$router->dispatch($method, $path);
// $router->get('/foods', function (){
//     echo 'Here are your foods';
// });
// $router->post('/foods', function(){
//     echo 'Creating a food';
// });
?>