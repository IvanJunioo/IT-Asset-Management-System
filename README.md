# IT-Asset-Management-System

#### File Directory Structure
- config
  - config
    system environment settings, base directories, and Database credentials
- public 
  frontend. accessible via browser.
  - css
  - partials
    modular php includes
  - script
    javascripts for dynamic html
  - views
    html/php
- src
  backend. inaccessible via browser.
  - handlers
    handles web requests from frontend
  - manager
    manages repos
  - model
    pure data objects or entities. independent.
  - repos
    low-level repositories for database query interfacing and access. depends on model.
  - utilities
- test 


- install composer require google/apiclient:^2.0
- Run in terminal
- 1. php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
- 2. php -r "if (hash_file('sha384', 'composer-setup.php') === 'c8b085408188070d5f52bcfe4ecfbee5f727afa458b2573b8eaaf77b3419b0bf2768dc67c86944da1544f06fa544fd47') { echo 'Installer verified'.PHP_EOL; } else { echo 'Installer corrupt'.PHP_EOL; unlink('composer-setup.php'); exit(1); }"
- 3. php composer-setup.php
- 4. php -r "unlink('composer-setup.php');"
- 5. php composer.phar require google/apiclient:^2.0
