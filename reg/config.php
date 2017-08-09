<?php
/**
 * Created by PhpStorm.
 * User: hardh
 * Date: 22.07.2017
 * Time: 18:46
 */
//Ключ защиты
if(!defined('REG_KEY'))
{
    header("HTTP/1.1 404 Not Found");
    exit(file_get_contents('reg/404.html'));
}


//Адрес базы данных
define('REG_DBSERVER','localhost');

//Логин БД
define('REG_DBUSER','host1608830_mlmuser');

//Пароль БД
define('REG_DBPASSWORD','hAH7hmVa');

//БД
define('REG_DATABASE','host1608830_mlm');

//Префикс БД
define('REG_DBPREFIX','reg_');

//Errors
define('REG_ERROR_CONNECT','Немогу соеденится с БД');

//Errors
define('REG_NO_DB_SELECT','Данная БД отсутствует на сервере');

//Адрес хоста сайта
define('REG_HOST','http://'. $_SERVER['HTTP_HOST'] .'/');

//Адрес почты от кого отправляем
define('REG_MAIL_AUTOR','wacbel@yandex.ru');
?>