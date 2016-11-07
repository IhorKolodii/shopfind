<?php
/* Boot script */
/* Autoloader coniguration */
spl_autoload_register(function ($class_name) {
    include 'includes/classes/'.$class_name . '.php';
});
/* Anything else */