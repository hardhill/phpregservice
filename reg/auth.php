<?php
/**
 * Created by PhpStorm.
 * User: hardh
 * Date: 24.07.2017
 * Time: 17:02
 */
//Если нажата кнопка то обрабатываем данные
$err=array();
if (isset($_POST['submit'])) {
    //Проверяем на пустоту
    if (empty($_POST['email']))
        $err[] = 'Не введен Логин';

    if (empty($_POST['pass1']))
        $err[] = 'Не введен Пароль';

    //Проверяем email
    if (emailValid($_POST['email']) === false)
        $err[] = 'Не корректный E-mail';

    //Проверяем наличие ошибок и выводим пользователю
    if (count($err) > 0)
        echo ServerOtvet(showErrorMessage($err), "error");
    else {
        /*Создаем запрос на выборку из базы
		данных для проверки подлиности пользователя*/
        $sql = 'SELECT * FROM `users` WHERE `mail_reg` = :email	AND `status` = 1';
        //Подготавливаем PDO выражение для SQL запроса
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':email', $_POST['email'], PDO::PARAM_STR);
        $stmt->execute();

        //Получаем данные SQL запроса
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        //Если логин совподает, проверяем пароль
        if (count($rows) > 0) {
            //Получаем данные из таблицы
            $pass = $_POST['pass1'];


            if (md5(md5($pass) . $rows[0]['salt']) == $rows[0]['password']) {
                // авторизовались
                session_start();
                $userid = uniqid();
                $userip = get_client_ip();
                $name = $rows[0]['login'];
                // записать в БД
                $sql = 'UPDATE `users` SET `active_hex`=:userid, `client_ip`=:userip WHERE `mail_reg`=:email';
                //Подготавливаем PDO выражение для SQL запроса
                $stmt = $db->prepare($sql);
                $stmt->bindValue(':email', $_POST['email'], PDO::PARAM_STR);
                $stmt->bindValue(':userip',$userip, PDO::PARAM_STR);
                $stmt->bindValue(':userid',$userid, PDO::PARAM_STR);
                $stmt->execute();
                if($stmt) {
                    $arr = array(
                        "userid" => md5($userid),
                        "userip" => $userip,
                        "email"=> $_POST['email']
                    );
                    $toclient = json_encode($arr);
                    echo ServerOtvet($toclient, "success");
                }
                exit;
            } else {
                echo ServerOtvet('Неверный пароль!', "error");
            }
        } else {
            echo ServerOtvet('Логин <b>' . $_POST['email'] . '</b> не найден!', "error");
        }
    }
}
//echo ServerOtvet("Auth complited","success");
?>