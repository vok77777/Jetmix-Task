<!DOCTYPE html>
<html lang="ru">
<head>
    <title>Восстановление пароля</title>
</head>
<body>
    <h2>Восстановление пароля</h2>
    <p>Для восстановления пароля, пожалуйста, перейдите по следующей ссылке:</p>
    <a href="{{ url('/') . '/recovery-password/?token=' . $params['token'] }}">Восстановить пароль</a>
    <p>Если вы не запрашивали восстановление пароля, просто проигнорируйте это письмо.</p>
</body>
</html>
