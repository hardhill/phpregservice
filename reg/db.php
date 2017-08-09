<?php
/**
 * Created by PhpStorm.
 * User: hardh
 * Date: 22.07.2017
 * Time: 19:09
 */
//Ключ защиты
if(!defined('REG_KEY'))
{
    header("HTTP/1.1 404 Not Found");
    exit(file_get_contents('reg/404.html'));
}

//Подключение к базе данных mySQL с помощью PDO
try {
    $db = new PDO('mysql:host='.REG_DBSERVER.';dbname='.REG_DATABASE, REG_DBUSER, REG_DBPASSWORD, array(
        PDO::ATTR_PERSISTENT => true
    ));

} catch (PDOException $e) {
    echo "Ошибка соединеия!: " . $e->getMessage() . "<br/>";
    die();
}