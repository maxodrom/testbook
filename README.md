Установка
=========

- Склонировать репозиторий
- Создать виртуальные хосты для backend и fronend:
```
<VirtualHost *:80>
    ServerAdmin max@maxodrom.ru
    DocumentRoot "C:/Program Files (x86)/Apache24/htdocs/testbook.loc/backend/web"
    ServerName testbook-backend.loc
	ServerAlias www.testbook-backend.loc
    ErrorLog "logs/testbook-backend.loc-error.log"
    CustomLog "logs/testbook-backend.loc-access.log" common
</VirtualHost>
<VirtualHost *:80>
    ServerAdmin max@maxodrom.ru
    DocumentRoot "C:/Program Files (x86)/Apache24/htdocs/testbook.loc/frontend/web"
    ServerName testbook-frontend.loc
	ServerAlias www.testbook-frontend.loc
    ErrorLog "logs/testbook-frontend.loc-error.log"
    CustomLog "logs/testbook-frontend.loc-access.log" common
</VirtualHost>
```
- Импорт БД из файла testbook.sql
- Для управления книгами перейти на страницу: http://testbook-backend.loc/book