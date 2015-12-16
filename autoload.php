<?php
/**
 * @author: Tiger <DropFan@Gmail.com>
 * @date: 2015/12/08
 */

defined('WEATHERLIB_DIR') || define('WEATHERLIB_DIR', dirname(__FILE__).'/../');

spl_autoload_register(function ($class) {
    $path = WEATHERLIB_DIR. str_replace("\\", "/", $class) . '.php';

    if (file_exists($path)) {
        $a = include($path);
        // echo "[$path]<br>";
    }
    // else {
        // $a = 'n';
        // echo "!$class [$path] file not found!!!<br>";
        //throw new Exception(" $class file not found. (path: [$path])", 1);
    // }
    // var_dump($a);
});
