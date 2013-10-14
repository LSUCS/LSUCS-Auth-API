<?php

    ini_set('display_errors','On');
    error_reporting(E_ALL);
    setlocale(LC_MONETARY, 'en_GB');

    include 'Auth.php';
    Lsucs_Auth::run();
    
?>