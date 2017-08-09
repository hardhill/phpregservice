<?php
/**
 * Created by PhpStorm.
 * User: hardh
 * Date: 22.07.2017
 * Time: 19:07
 */

function sendMessageMail($to, $from, $title, $message)
{
    require_once "SendMailSmtpClass.php"; // подключаем класс
    $mailSMTP = new SendMailSmtpClass($from, 'C0rratec', 'ssl://smtp.yandex.ru', 'No-reply', 465);

    // заголовок письма
    $headers= "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=utf-8\r\n"; // кодировка письма
    $headers .= "From: ". $from ."\r\n";
    //Формируем заголовок письма
    $subject = $title;

    $result =  $mailSMTP->send($to, $subject, $message, $headers); // отправляем письмо
// $result =  $mailSMTP->send('Кому письмо', 'Тема письма', 'Текст письма', 'Заголовки письма');
    if($result === true){
        error_log("Post sending");
        return true;
    }else{
        error_log("Post do not sending ".$result);
        return "Письмо не отправлено. Ошибка: " . $result;
    }

}

/**функция вывода ошибок
 * @param array  $data
 */
function showErrorMessage($data)
{
    $err = '<ul>'."\n";

    if(is_array($data))
    {
        foreach($data as $val)
            $err .= '<li style="color:#ff2a21;">' . $val .'</li>'."\n";
    }
    else
        $err .= '<li style="color:red;">'. $data .'</li>'."\n";

    $err .= '</ul>'."\n";

    return $err;
}

function ServerOtvet(string $in, string $status)
{

    $otvet = array(
        "status"=> "{$status}",
        "message" => "{$in}"
    );
    return json_encode($otvet);
}

/**Простой генератор соли
 * @param string  $sql
 */
function salt()
{
    $salt = substr(md5(uniqid()), -8);
    //$salt = "AAA";
    return $salt;
}

/** Проверка валидации email
 * @param string $email
 * return boolian
 */
function emailValid($email){
    if(function_exists('filter_var')){
        if(filter_var($email, FILTER_VALIDATE_EMAIL)){
            return true;
        }else{
            return false;
        }
    }else{
        if(!preg_match("/^[a-z0-9_.-]+@([a-z0-9]+\.)+[a-z]{2,6}$/i", $email)){
            return false;
        }else{
            return true;
        }
    }
}

function get_client_ip(){
    $ipaddress = '';
    if (isset($_SERVER['HTTP_CLIENT_IP']))
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_X_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if(isset($_SERVER['REMOTE_ADDR']))
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}
?>