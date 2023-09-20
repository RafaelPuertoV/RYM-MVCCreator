<?php
# USE: $configs = include('config.php');

return array(
    # Server Config
    'hostDB' => 'mysqlServ',
    'usernameDB' => 'root',
    'passwordDB' => '',
    'nameDB' => 'my_dbname',
    'MVC_PATH'=> __DIR__.'/build/',
    'MVC_NAMESPACE'=> 'RYMAPP',
    'MVC_PREFIX'=> 'RYMAPP',
    # General Config
    'timezone' => 'America/Mexico_City',
);

?>