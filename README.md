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

`sudo a2ensite backend.webfood.local.conf webfood.local.conf`

`a2enmod rewrite`

`systemctl restart apache2`


#### Настройка PostgreSQL:

`sudo apt-get install postgresql postgresql-contrib`

После установки mysql выполняем команды:

`sudo systemctl enable postgresql`

`sudo systemctl start postgresql`

`sudo -u postgres psql`

`ALTER USER postgres WITH PASSWORD '1234';`

`CREATE DATABASE webfood ENCODING 'UTF-8' LC_COLLATE 'ru_RU.UTF-8' LC_CTYPE 'ru_RU.UTF-8';`

`GRANT ALL PRIVILEGES ON DATABASE  webfood TO postgres;`

#### Настройка локалей, если возникает ошибка при создании базы:

`sudo systemctl stop postgresql@9.5-main`

`sudo pg_dropcluster --stop 9.5 main`

`sudo pg_createcluster --locale ru_RU.UTF-8 --start 9.5 main`

`sudo systemctl start postgresql@9.5-main`

#### Подключение к db в PhpStorm:

хост:   `localhost`

порт:   `5432`

база:   `webfood`

юзер:   `postgres`

пароль: `1234`

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


### **Настройка cron:**

Он уже должен быть подключен в композере, если нет, то:

`composer require --prefer-dist yii2tech/crontab`

`composer update`

Проверяем, работает ли демон:

`ps ax | grep [c]ron`

Если нет, то запускаем его:

`/etc/init.d/cron start`

Для запуска контроллера задач в cron:

`php yii task/init`

GitHub: https://github.com/yii2tech/crontab

Мануал: https://code.tutsplus.com/ru/tutorials/scheduling-tasks-with-cron-jobs--net-8800


### **Запуск тестов:**

Из корня запустить команду:

`php ./vendor/bin/codecept run`

Если ошибка, перед этим сделать: 

`composer install`

`php ./vendor/bin/codecept build`


### **Почтовый сервер**

E-mail: `webfood.test@gmail.com`

Пароль: `GrDM7b6h57HvZCq`
