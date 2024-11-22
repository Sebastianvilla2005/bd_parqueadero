<?php

function myAutoloader($class) {

    $path = 'clases/' . $class . '.php';
    

    if (file_exists($path)) {
        require_once $path;
    } else {

        echo "La clase '$class' no se encuentra.";
    }
}


spl_autoload_register('myAutoloader');


?>