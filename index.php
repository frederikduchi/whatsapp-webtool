<?php
session_start();

ini_set('display_errors', true);
error_reporting(E_ALL);
/*
unset($_SESSION['upload-success']);
unset($_SESSION['upload-next']);
unset($_SESSION['error']);
*/
$routes = array(
  'home' => array(
    'controller' => 'Upload',
    'action' => 'index'
  ),
  'upload' => array(
    'controller' => 'Upload',
    'action' => 'upload'
  ),
  'conversation' => array(
    'controller' => 'Conversation',
    'action' => 'index'
  )
);

if(empty($_GET['page'])) {
  $_GET['page'] = 'home';
}
if(empty($routes[$_GET['page']])) {
  header('Location: index.php');
  exit();
}

$route = $routes[$_GET['page']];
$controllerName = $route['controller'] . 'Controller';

require_once __DIR__ . '/controller/' . $controllerName . ".php";

$controllerObj = new $controllerName();
$controllerObj->route = $route;
$controllerObj->filter();
$controllerObj->render();

?>