Free Quest Prototype No 42
-------------------------------------
Free Quest Prototype No42 based on Symphony2 components.
Training application to improve knowledge on Symfony2, Doctrine, Google services integration, etc.

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
            # "Require all granted" for Apache 2.4
        </Directory>

        ErrorLog "logs/fq-error.log"
        CustomLog "logs/fq-access.log" common
    </VirtualHost>
    
Reinstall environment:

    php app/console doctrine:database:drop --force
    php app/console doctrine:database:create
    php app/console doctrine:schema:update --force
    rm -rf web/files
    php app/console cache:clear --env=prod
    php app/console cache:clear --env=dev

Execute next command if entities were updated:

    php app/console doctrine:generate:entities FqBundle

Clear dns cache for chrome:

    chrome://net-internals/#dns

Temp fix for curl ssl:

Download file: http://curl.haxx.se/ca/cacert.pem
Update php.ini:

    curl.cainfo = "<path to file>\cacert.pem"

