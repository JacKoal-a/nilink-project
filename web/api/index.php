<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization');
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json; charset=UTF-8');
$method = $_SERVER['REQUEST_METHOD'];
if ($method == "OPTIONS") {
    header('Access-Control-Allow-Origin: *');
    header("Access-Control-Allow-Headers: X-API-KEY, Origin, X-Requested-With, Content-Type, Accept, Access-Control-Request-Method,Access-Control-Request-Headers, Authorization");
    header("HTTP/1.1 200 OK");
    die();
}
include_once "loader.php";

//Error log
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

//USER
include_once 'routes/UserRoutes.php';

//DEVELOPER
include_once 'routes/DeveloperRoutes.php';

//OAUTH
include_once 'routes/OAuthRoutes.php';

//APPLICATION
include_once 'routes/ApplicationRoutes.php';

//LOBBY
include_once 'routes/LobbyRoutes.php';

include_once 'routes/ImageRoutes.php';

Route::pathNotFound(function($path){
  http_response_code(404);
  echo json_encode(array("error"=>"Path '$path' not found"));
});

Route::methodNotAllowed(function($path,$method){
  http_response_code(405);
  echo json_encode(array("error"=>"Method '$method' not allowed"));
});

Route::run('/api');
?>