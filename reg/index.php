<?php
/**
 * Created by PhpStorm.
 * User: hardh
 * Date: 22.07.2017
 * Time: 18:45
 */
//Запускаем сессию
//session_start();

//Устанавливаем кодировку и вывод всех ошибок
//header('Content-Type: text/html; charset=UTF8');
error_reporting(E_ALL);

//Включаем буферизацию содержимого
//ob_start();


//Определяем переменную для переключателя

$mode=isset($_REQUEST['mode'])?$_REQUEST['mode']: false;

$user = isset($_SESSION['user']) ? $_SESSION['user'] : false;
$err = array();


//Устанавливаем ключ защиты
define('REG_KEY', true);

//Подключаем конфигурационный файл
include_once 'config.php';



//Подключаем скрипт с функциями
include_once 'funct.php';


//подключаем MySQL
include_once 'db.php';
error_log("Db connected");

switch ($mode) {
    case 'reg':
        include_once "reg.php";
        break;
    case 'auth':
        include_once "auth.php";
        break;
    case 'sess':
        include_once "sess.php";
        break;
    default:
        echo json_encode($err);
}