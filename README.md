# Описание

Простое приложение для получения реферального параметра пользователя из сссылки на чат ВК при его начале.

## Установка
```
composer install
```
## Все настройки в .env в корне проекта
```
VK_CONFIRMATION_TOKEN = ''
VK_APP_ID = '' 
VK_APP_SECRET = ''
VK_REDIRECT_URI = ''
```

Проект разрабатывался в условиях отсутствия базы данных, поэтому используется хранение данных в файлах .json.