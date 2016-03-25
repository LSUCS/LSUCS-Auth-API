<?php
    require(ROOT . '/../password.php');
 
    // External key for access to this API
    $config['key'] = $password;
    $password = "";

    // Log file name
    $config['log_file'] = ROOT . '/../auth.log';

    // The mechanism file to load
    $config['mechanism'] = 'Mechanism_Xenforo';

    // Root for Xenforo mechanism
    $config['xfdir'] = "/srv/http/soc_lsucs/lsucs.org.uk/htdocs";

    $config['fol_group'] = 20;
    $config['member_group'] = 19;
    
?>
