<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Заявка от {{ $params['email'] }}</title>
</head>
<body>
<h2>Данные заявки</h2>
<p><strong>Тема заявки:</strong> {{ $params['topic'] }}</p>
<p><strong>Сообщение:</strong> {{ $params['message'] }}</p>
<p><strong>Имя пользователя:</strong> {{ $params['name'] }}</p>
<p><strong>Почта пользователя:</strong> {{ $params['email'] }}</p>

@if($params['attachments'] && count($params['attachments']))
    <h3>Прикрепленные файлы</h3>
    <ul>
        @foreach($params['attachments'] as $attachment)
            <li><a href="{{ url('/') . '/' . $attachment->path }}" target="_blank">{{ $attachment->file_name }}</a></li>
        @endforeach
    </ul>
@endif
</body>
</html>
