# RYM MVC Creator


This is a project for create an example of Model-View-Controller (MVC) framework

Components of MVC :
The MVC framework includes the following 3 components:

* Controller
* Model
* View


How to use:

1. Download the RYMMVCCreator.zip and extract the project to [www_root]/RYMMVC/.
1. Configure: Edit the config.php file:
    ```php
    return array(
        # Server Config
        'hostDB' => '{{MySQL.SERVER}}',
        'usernameDB' => '{{DB.USER}}',
        'passwordDB' => '{{DB.PASSWORD}}',
        'nameDB' => '{{DB.NAME}}',
        'MVC_PATH'=> __DIR__.'/build/',
        'MVC_NAMESPACE'=> '{{MYNAMESPACE}}',
        'MVC_PREFIX'=> '{{CLASS.PREFIX}}',
        # General Config
        'timezone' => 'America/Mexico_City',
    );
    ```
1. Go to your web browser to http://localhost/RYMMVC/
1. Execute in shell:
    ```
    $> cd build/{{MYNAMESPACE}}/
    $> composer install
    ```
