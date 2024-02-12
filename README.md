##Инструкция по развертке тестового задания

_Тестовое задание было выполнено локально, используя Docker и пакет Laravel Sail._</br>
_Если при оценке задания Вы будете использовать Docker, то достаточно подключить зависимости из
composer.json, установить Laravel Sail и поднять контейнер через sail up_.</br>
_При установке Laravel Sail - стоит выбрать PostgreSQL и пакет Mailpit для проверки почтовых отправлений._

###Общие шаги:
__1.__ Накатить миграции через ```sail artisan migrate```</br>
__2.__ Запустить сидеры - </br>сначала ```sail artisan db:seed --class=RolesSeeder```, </br>а затем ```sail artisan db:seed --class=UsersSeeder```

_Для удобства, здесь поставлен пакет ```darkaonline/l5-swagger```, который выступает в качестве
документации к API._</br>
_Сам Swagger находится по пути ```/api/documentation```._</br>
_Для генерации API документации ```sail artisan l5-swagger:generate```_.

