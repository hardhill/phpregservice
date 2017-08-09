<?php
/**
 * Created by PhpStorm.
 * User: hardh
 * Date: 31.07.2017
 * Time: 20:48
 */

if(!defined('REG_KEY'))
{
    header("HTTP/1.1 404 Not Found");
    exit(file_get_contents('reg/404.html'));
}

    $userid = isset($_POST['userid'])? $_POST['userid']: "0";
    $userip = isset($_POST['userip'])? $_POST['userip']: "0";
    $email = isset($_POST['email'])? $_POST['email']: null;

    $sql = "SELECT * FROM `users` WHERE `mail_reg`=:email AND `client_ip` = :userip";
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':email', $email, PDO::PARAM_STR);
    $stmt->bindValue(':userip', $userip, PDO::PARAM_STR);
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    if(count($rows)>0){
        if($userid == md5($rows[0]['active_hex'])){
            $data = array(
                "login"=>$rows[0]['login'],
                "email"=>$rows[0]['mail_reg']
            );
            echo ServerOtvet(json_encode($data),"success");
            exit;
        }
        else
            $err[] = "Old session";
    }
    else
        $err[] = "Authorization need";
    if(count($err)>0)
        echo ServerOtvet(showErrorMessage($err),"error");

?>