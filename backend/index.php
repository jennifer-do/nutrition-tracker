<?php
// import Composer's autoloader.
require_once __DIR__ . '/vendor/autoload.php';

// loading the autoloader lets PHP understand dependencies (like Dotenv) without you having to require-once-ing the dependencies' source files
// Dotenv is a parser for .env
use Dotenv\Dotenv;
use Jenniferd\NutritionApi\Database;
use Jenniferd\NutritionApi\Router;
use Jenniferd\NutritionApi\ApiFunctions;

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
    $params = [];
    $where = [];
    if (isset($_GET['limit'])){
        $limit = ' LIMIT ' . $_GET['limit'];
    }
    if (isset($_GET['date'])){
        $date = new DateTime($_GET['date']);
        $next_day = (clone $date)->modify('+1 day');
        $where[] = "date >= :date_from AND date < :date_to";
        $params['date_from'] = $date->format('Y-m-d');
        $params['date_to'] = $next_day->format('Y-m-d');
    }
    if (isset($_GET['date_from'])){
        $where[] = "date >= :date_from";
        $params['date_from'] = $_GET['date_from'];
    }
    if (isset($_GET['date_to'])){
        $where[] = "date <= :date_to";
        $params['date_to'] = $_GET['date_to'];
    }
    if (isset($_GET['food_item_search'])){
        //$food_search = implode($_GET['food_item_search']);
        $food_search = $_GET['food_item_search'];
        $food_search = ApiFunctions::arrayToWildCardArray($food_search);
        $where[] = "food_name IN  ANY(ARRAY[$food_search])";
    }
    if (isset($_GET['order_by'])){
        $order = " ORDER BY " . $_GET['order_by'];
        $order .= $_GET['order_direction'] ?? ' DESC';
    }

    $sql = 'SELECT * FROM consumption_log';
    if (count($where) > 0){
        $where_clause = ' WHERE ' . implode(' AND ',$where);
        $sql .= $where_clause;
    }
    if (isset($order))
        $sql .= $order;
    if (isset($limit))
        $sql .= $limit;

    $stmt = $db->prepare($sql);
    $stmt->execute();
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