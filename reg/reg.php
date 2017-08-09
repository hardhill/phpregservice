<?php
/**
 * Created by PhpStorm.
 * User: hardh
 * Date: 22.07.2017
 * Time: 20:29
 */
/*Если нажата кнопка на регистрацию,
 начинаем проверку*/


if(isset($_GET['key']))
{

    //Проверяем ключ
    $sql = 'SELECT * FROM `users` WHERE `active_hex` = :key';
    //Подготавливаем PDO выражение для SQL запроса
    $stmt = $db->prepare($sql);
    $stmt->bindValue(':key', $_GET['key'], PDO::PARAM_STR);
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if(count($rows) == 0)
        $err[] = 'Ключ активации не верен!';

    //Проверяем наличие ошибок и выводим пользователю
    if(count($err) > 0) {

        echo showErrorMessage($err);
    }
    else
    {
        //Получаем адрес пользователя
        $email = $rows[0]['mail_reg'];

        //Активируем аккаунт пользователя
        $sql = 'UPDATE `users` SET `status`=1 WHERE `mail_reg`=:email';
        //Подготавливаем PDO выражение для SQL запроса
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':email', $email, PDO::PARAM_STR);
        $stmt->execute();

        //Отправляем письмо для активации
        $title = 'Ваш аккаунт на http://bielecki.ru успешно активирован';
        $message = 'Поздравляю Вас, Ваш аккаунт на http://bielecki.ru успешно активирован';

        sendMessageMail($email, REG_MAIL_AUTOR, $title, $message);

        /*Перенаправляем пользователя на
        нужную нам страницу*/
        //header('Location:'. REG_HOST .'sitetwo/auth.html');
        exit;
    }
}

//----------------------------------------------------------------------------------------------------------------------

if (isset($_POST['submit'])) {

    //Утюжим пришедшие данные
    if (empty($_POST['email']))
        $err[] = 'Поле Email не может быть пустым!';
    else {
        if (emailValid($_POST['email']) === false)
            $err[] = 'Не правильно введен E-mail' . "\n";
    }

    if (empty($_POST['pass1']) || strlen($_POST['pass1'])<8)
        $err[] = 'Поле Пароль не может быть пустым или меньше 8 символов';

    if ($_POST['pass1'] != $_POST['pass2'])
        $err[] = 'Пароли указаны разные. Напишите правильно.';

    //Проверяем наличие ошибок и выводим пользователю
    if (count($err) > 0)
        echo ServerOtvet(showErrorMessage($err), "error");
    else {
        // все верно с полями. приступаем к проверке логина
        /*Проверяем существует ли у нас
			такой пользователь в БД*/
        $sql = 'SELECT `login` FROM `users` WHERE `mail_reg`=:email';
        //Подготавливаем PDO выражение для SQL запроса
        $stmt = $db->prepare($sql);
        $stmt->bindValue(':email', $_POST['email'], PDO::PARAM_STR);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($rows) > 0)
            $err[] = 'К сожалению Логин: <b>' . $_POST['email'] . '</b> занят!';

        if (count($err) > 0)
            echo ServerOtvet(showErrorMessage($err), "error");
        else {
            //Получаем ХЕШ соли
            $salt = salt();
            $username = htmlspecialchars($_POST['username']);
            //Солим пароль
            $pass = md5(md5($_POST['pass1']) . $salt);
            //текущее время
            $curtime = time();
            /*Если все хорошо, пишем данные в базу*/
            $sql = 'INSERT INTO `users` (`login`, `password`, `salt`, `active_hex`, `status`, `mail_reg`, `last_act`, `reg_date`)
                    VALUES(:login, :pass, :salt, :hex, 0, :email, :lastact, :reg_date)';
            //Подготавливаем PDO выражение для SQL запроса
            $stmt = $db->prepare($sql);
            $stmt->bindValue(':login', $username, PDO::PARAM_STR);
            $stmt->bindValue(':pass', $pass, PDO::PARAM_STR);
            $stmt->bindValue(':salt', $salt, PDO::PARAM_STR);
            $stmt->bindValue(':hex', md5($salt), PDO::PARAM_STR);
            $stmt->bindValue(':email', $_POST['email'], PDO::PARAM_STR);
            $stmt->bindValue(':lastact', 0, PDO::PARAM_INT);
            $stmt->bindValue(':reg_date', $curtime, PDO::PARAM_INT);
            $stmt->execute();

            //Отправляем письмо для активации
            $url = REG_HOST . 'reg?mode=reg&key=' . md5($salt); //адрес ссылки из письма
            $title = 'Регистрация на http://mlm.bielecki.ru';
            $message = 'Для активации Вашего акаунта пройдите по ссылке <a href="' . $url . '">' . $url . '</a> '.'Затем после подтверждения активации перейдите на главную страницу для входа.';
            $e=sendMessageMail($_POST['email'], REG_MAIL_AUTOR, $title, $message);
            if($e!=true) {
                $err[] = $e;
                echo ServerOtvet($e, "error");
            }else
                echo ServerOtvet("Регистрация выполнена. На Вашу почту отправлено письмо для активации учетной записи.", "regsave");
            exit;
        }
    }
} else
    http_response_code(404);

?>