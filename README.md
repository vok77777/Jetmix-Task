**Инструкция по развертке тестового задания**

Аккаунт менеджера
* Email: manager@mail.ru
* Password: 123123123

_Тестовое задание было выполнено локально, используя **Docker** и пакет **Laravel Sail**._</br>
_Если при оценке задания Вы будете использовать **Docker**, то достаточно подключить зависимости из
**composer.json**, установить **Laravel Sail** и поднять контейнер через ```sail up```, либо ```./vendor/bin/sail up```_.</br>
_При установке **Laravel Sail** - стоит выбрать **PostgreSQL** и пакет **Mailpit** для проверки почтовых отправлений._

**Общие шаги:**</br>
__1.__ Накатить миграции через ```sail artisan migrate```</br>
__2.__ Запустить сидеры - </br>сначала ```sail artisan db:seed --class=RolesSeeder```, </br>а затем ```sail artisan db:seed --class=UsersSeeder```

_Для удобства, здесь поставлен пакет ```darkaonline/l5-swagger```, который выступает в качестве
документации к API._</br>
_Сам Swagger находится по пути ```/api/documentation```._</br>
_Для генерации API документации ```sail artisan l5-swagger:generate```_.

**Окружение:**
1. WSL 2
2. Ubuntu 22.04
3. Docker 4.27.2
4. PHP 8.1
