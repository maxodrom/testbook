Тестовое задание
================

![](https://github.com/maxodrom/testbook/blob/master/test-task.png)

Сделать на Yii2 возможность только зарегистрированным пользователям просматривать, удалять, редактировать записи в таблице "books"

|books|
id,
name,
date_create, / дата создания записи
date_update, / дата обновления записи
preview, / путь к картинке превью книги
date, / дата выхода книги
author_id / ид автора в таблице авторы 

|authors| редактирование таблицы авторов не нужно, необходимо ее просто заполнить тестовыми данными.
id,
firstname, / имя автора
lastname,  / фамилия автора 

в итоге страница управления книгами должна выглядеть так: http://dl.dropbox.com/u/14927161/Selection_214.png
ТЗ рассчитано на сутки. Необходимо выгрузить код на github или bitbucket.

Установка
=========

![](https://github.com/maxodrom/testbook/blob/master/booksapp.png)

- Склонировать репозиторий
- Создать виртуальные хосты для backend и fronend (например, так):
```
<VirtualHost *:80>
    DocumentRoot "C:/Program Files (x86)/Apache24/htdocs/testbook.loc/backend/web"
    ServerName testbook-backend.loc
	ServerAlias www.testbook-backend.loc
    ErrorLog "logs/testbook-backend.loc-error.log"
    CustomLog "logs/testbook-backend.loc-access.log" common
</VirtualHost>
<VirtualHost *:80>
    DocumentRoot "C:/Program Files (x86)/Apache24/htdocs/testbook.loc/frontend/web"
    ServerName testbook-frontend.loc
	ServerAlias www.testbook-frontend.loc
    ErrorLog "logs/testbook-frontend.loc-error.log"
    CustomLog "logs/testbook-frontend.loc-access.log" common
</VirtualHost>
```
- Импорт БД из файла testbook.sql
- Настроить корректное соединение с БД в файле common/config/main.php
- При необходимости выполнить: 
``` 
composer update
```
в корневой директории проекта.
- Работа по управлению книгами происходит в backend на странице: http://testbook-backend.loc/book
- Требования: PHP5.5+, Mysql5.5+, Yii2, Composer, bower
- Логин и пароль для входа в backend: admin и 123456 соответственно. 