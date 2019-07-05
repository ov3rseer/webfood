**Как скачать проект:**

В папке где будет находиться проект нужно выполнить команду:

`git clone http://adlibtech.ru/git/alvastudio/WebFood.git`

Подключение к db в PhpStorm:

`jdbc:mysql://localhost:3306/webfood?useJDBCCompliantTimezoneShift=true&useLegacyDatetimeCode=false&serverTimezone=UTC`

Чтобы заполнить базу данных таблицами:

`php yii migrate`


**Правила:**

Перед тем как локально вносить изменения, необходимо сделать новую ветку. 
После внесения изменений выполнить команды:

`git commit`

`git push`

Создаем новый Pull Request от своей ветки.

**`НИ В КОЕМ СЛУЧАЕ НЕ ДЕЛАЕМ COMMIT В MASTER!!!`**


**Запуск тестов:**

Из корня запустить команду:

`php ./vendor/bin/codecept run`

Если ошибка, перед этим сделать: 

`composer install`

`php ./vendor/bin/codecept build`
