Free Quest Prototype No 42
-------------------------------------
Free Quest Prototype No42 based on Symphony2 components.

Installation
-----------------------------------------
Installation steps:

Run composer install command in project root directory.

Create virtual host:

    <VirtualHost *:80>
        ServerName freequest.com.ua
        ServerAlias www.freequest.com.ua

        DocumentRoot "<project_path>/web"

        ServerAdmin your_email@example.com
            <Directory "<project_path>/web">
            # enable the .htaccess rewrites
            AllowOverride All
            Order allow,deny
            Allow from All
        </Directory>

        ErrorLog "logs/fq-error.log"
        CustomLog "logs/fq-access.log" common
    </VirtualHost>

Install entities and database:

    php app/console doctrine:database:drop --force
    php app/console doctrine:database:create
    php app/console doctrine:generate:entities FqBundle
    php app/console doctrine:schema:update --force

