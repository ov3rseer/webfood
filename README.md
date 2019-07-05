### **Установка:**

#### Настройка сервера Apache:

Из корня / выполнить команды:

`cd etc`

`sudo gedit hosts`

Внести изменения в файл hosts:

`127.0.0.1 backend.webfood.local webfood.local`

Далее выполняем команды:

`cd apache2/sites-available/`

`sudo cp 000-default.conf backend.webfood.local.conf`

`sudo cp 000-default.conf webfood.local.conf`

Внести изменения в файлы (обратить внимание на <путь к проекту> далее):

`sudo gedit backend.webfood.local.conf `

    <VirtualHost *:80>
        ServerName backend.webfood.local
        DocumentRoot "/<путь к проекту>/webfood/backend/web"      
        <Directory "/<путь к проекту>/webfood/backend/web">
            RewriteEngine on
            RewriteCond %{REQUEST_FILENAME} !-f
            RewriteCond %{REQUEST_FILENAME} !-d
            RewriteRule . index.php
            DirectoryIndex index.php
            Require all granted
        </Directory>
    </VirtualHost>

`sudo gedit webfood.local.conf `

    <VirtualHost *:80>
        ServerName webfood.local
        DocumentRoot "/<путь к проекту>/webfood/frontend/web"      
        <Directory "/<путь к проекту>/webfood/frontend/web">
            RewriteEngine on
            RewriteCond %{REQUEST_FILENAME} !-f
            RewriteCond %{REQUEST_FILENAME} !-d
            RewriteRule . index.php
            DirectoryIndex index.php
            Require all granted
        </Directory>
    </VirtualHost>

#### Настройка MySql:

После установки mysql выполняем команды:

`sudo mysql -u root`

`ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY '1234';`

`FLUSH PRIVILEGES;`

`CREATE DATABASE webfood CHARACTER SET utf8 COLLATE utf8_general_ci;`

`FLUSH PRIVILEGES;`

хост: `localhost`

порт: `3306`

база: `webfood`

юзер: `root`

пароль: `1234`

#### Подключение к db в PhpStorm:

`jdbc:mysql://localhost:3306/webfood?useJDBCCompliantTimezoneShift=true&useLegacyDatetimeCode=false&serverTimezone=UTC`


### **Как скачать проект:**

В папке где будет находиться проект нужно выполнить команду:

`git clone http://adlibtech.ru/git/alvastudio/WebFood.git`

Чтобы заполнить базу данных таблицами:

`php yii migrate`


### **Правила:**

Перед тем как локально вносить изменения, необходимо сделать новую ветку. 
После внесения изменений выполнить команды:

`git commit`

`git push`

Создаем новый Pull Request от своей ветки.

**`НИ В КОЕМ СЛУЧАЕ НЕ ДЕЛАЕМ COMMIT В MASTER!!!`**


### **Запуск тестов:**

Из корня запустить команду:

`php ./vendor/bin/codecept run`

Если ошибка, перед этим сделать: 

`composer install`

`php ./vendor/bin/codecept build`
