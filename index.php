<?php

define('ROOT', __DIR__ . DIRECTORY_SEPARATOR);
define('APP', ROOT . 'app'. DIRECTORY_SEPARATOR );


// load application config (error reporting etc.)
require APP . '/config/config.php';


//load routs
require APP . '/routes/web.php';


// load core class
foreach (scandir(APP .'/core/') as $filename) {

    $path = APP .'/core/' . $filename;

    if (is_file($path)) {
        require $path;
    }
}


// start the application
$app = new Application();
