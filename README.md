## Тестовое задание для SLAVA

Мой телеграм - [@repsys](https://t.me/repsys) (На случай если что-то не работает)

### Как запустить:

1. Клонировать проект:
    ```
    git clone https://github.com/Repsys/php-interview-slava.git
    ```
2. Выполнить в корне проекта:
    ```
    cp .env.example .env
    composer i
    docker-compose up -d
    sudo chmod 777 -R ./storage/
    docker-compose exec php-fpm php artisan key:generate
    docker-compose exec php-fpm php artisan migrate
    ```
3. Перейти по адресу http://localhost:8099/docs/oas
4. Слева две раскрывающихся группы апих: `excel_files` и `rows`


### Описание ТЗ
Laravel тестовое задание - [Ссылка на ТЗ](https://docs.google.com/document/d/1GDsAQP5xdx0lKNlxew6hHf3hSQx84GSprCWVaIA4o8E/edit?hl=en)

Создать laravel-проект в git-репозитории (подойдет любой публичный сервис, например github). Первым коммитом залить чистый фреймворк, следом — реализацию задания.

1. Реализовать контроллер с валидацией и загрузкой excel файла.
2. Доступ к контроллеру загрузки закрыть basic-авторизацией.
3. Загруженный файл через jobs поэтапно (по 1000 строк) парсить в БД (таблица rows).
4. Прогресс парсинга файла хранить в redis (уникальный ключ + количество обработанных строк).
5. Поля excel:
6. id
7. name
8. date (d.m.Y)
9. Для парсинга excel можете использовать любой пакет из composer, за исключением maatwebsite/excel
10. Реализовать контроллер для вывода импортированных данных (rows) с группировкой по date - двумерный массив
11. Будет плюсом если вы реализуете через laravel echo передачу event-а на создание записи в rows
12. Будет плюсом написание тестов

Пример файла для импорта: https://yadi.sk/i/YuwPGwcIzv1DBQ

| id | name  | date     |
|----|-------|----------|
| 1  | Denim | 13.10.20 |
| 2  | Denim | 14.10.20 |
| 3  | Denim | 15.10.20 |
| 4  | Denim | 16.10.20 |
| 5  | Denim | 17.10.20 |
