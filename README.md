### **Установка:**

#### Настройка nginx

Есть тут: https://github.com/mickgeek/yii2-advanced-one-domain-config

#### Настройка сервера Apache:

Из корня / выполнить команды:

`cd etc`

`sudo gedit hosts`

Внести изменения в файл hosts:

`127.0.0.1 webfood.local`

Далее выполняем команды:

`cd apache2/sites-available/`

`sudo cp 000-default.conf webfood.local.conf`

Внести изменения в файл (обратить внимание на <путь к проекту> далее):

`sudo gedit webfood.local.conf`

        <VirtualHost *:80>
           ServerName webfood.local      
           #ErrorLog /var/log/apache2/advanced.local.error.log
           #CustomLog /var/log/apache2/advanced.local.access.log combined
           AddDefaultCharset UTF-8
        
           Options FollowSymLinks
           DirectoryIndex index.php index.html
           RewriteEngine on
        
           RewriteRule /\. - [L,F]
        
           DocumentRoot /var/www/webfood_extend/frontend/web
           <Directory /var/www/webfood_extend/frontend/web>
               AllowOverride none
               <IfVersion < 2.4>
                 Order Allow,Deny
                 Allow from all
               </IfVersion>
               <IfVersion >= 2.4>
                 Require all granted
               </IfVersion>
        
               # if a directory or a file exists, use the request directly
               RewriteCond %{REQUEST_FILENAME} !-f
               RewriteCond %{REQUEST_FILENAME} !-d
               # otherwise forward the request to index.php
               RewriteRule ^ index.php [L]
           </Directory>
        
           # redirect to the URL without a trailing slash (uncomment if necessary)
           #RewriteRule ^/admin/$ /admin [L,R=301]
        
           Alias /admin /var/www/webfood_extend/backend/web
           # prevent the directory redirect to the URL with a trailing slash
           RewriteRule ^/admin$ /admin/ [L,PT]
           <Directory /var/www/webfood_extend/backend/web>
               AllowOverride none
               <IfVersion < 2.4>
                   Order Allow,Deny
                   Allow from all
               </IfVersion>
               <IfVersion >= 2.4>
                   Require all granted
               </IfVersion>
        
               # if a directory or a file exists, use the request directly
               RewriteCond %{REQUEST_FILENAME} !-f
               RewriteCond %{REQUEST_FILENAME} !-d
               # otherwise forward the request to index.php
               RewriteRule ^ index.php [L]
           </Directory>
           
           Alias /terminal /var/www/webfood_extend/terminal/web
           # prevent the directory redirect to the URL with a trailing slash
           RewriteRule ^/terminal$ /terminal/ [L,PT]
           <Directory /var/www/webfood_extend/terminal/web>
               AllowOverride none
               <IfVersion < 2.4>
                   Order Allow,Deny
                   Allow from all
               </IfVersion>
               <IfVersion >= 2.4>
                   Require all granted
               </IfVersion>
        
               # if a directory or a file exists, use the request directly
               RewriteCond %{REQUEST_FILENAME} !-f
               RewriteCond %{REQUEST_FILENAME} !-d
               # otherwise forward the request to index.php
               RewriteRule ^ index.php [L]
           </Directory>
        </VirtualHost>

`sudo a2ensite webfood.local.conf`

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