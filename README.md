
**Подключение к db в PhpStorm**

`jdbc:mysql://localhost:3306/webfood?useJDBCCompliantTimezoneShift=true&useLegacyDatetimeCode=false&serverTimezone=UTC`

**Запуск тестов:**

Из корня запустить команду:
`php ./vendor/bin/codecept run`

Если ошибка, перед этим сделать: 

`composer install`

`php ./vendor/bin/codecept build`
