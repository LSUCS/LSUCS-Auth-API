<?php
    ini_set('display_errors','On');
    error_reporting(E_ALL);
    setlocale(LC_MONETARY, 'en_GB');

    // Webroot
    define('ROOT', $_SERVER['DOCUMENT_ROOT']);

    require_once('Auth.php');
    Lsucs_Auth::run();
?>
