<?php

    require_once 'Mechanism.php';
    require_once 'User.php';

    class Lsucs_Auth {

        private static $log;
    
        public static function run() {
        
            include 'config.php';
            
            self::$log = fopen('auth.log', 'a');
            fputs(self::$log, date('Y-m-d H:i:s') . " Incoming request from " . $_SERVER['REMOTE_ADDR'] . "\n");

            //Check key
            if (!isset($_POST["key"]) || $_POST["key"] != $config['key']) {
                self::error(0);
            }
            
            //fputs(self::$log, "Data:\n" . print_r($_REQUEST, true) . "\n");

            //Load auth mechanism
            require_once 'Mechanism/' . $config['mechanism'] . '.php';
            $mechanism = new $config['mechanism'];
            
            //Handle api request
            $mechanism->handleRequest();
        
        }
        
        public static function error($code) {
            switch ($code) {
                case 0: $message = 'Invalid API key'; break;
                case 1: $message = 'Invalid API method'; break;
                case 2: $message = 'Missing method parameters'; break;
                case 3: $message = 'Match not found'; break;
                case 4: $message = 'Invalid parameter type'; break;
                default: $message = 'Unknown error'; break;            
            }
            fputs(self::$log, 'ERROR: ' . $message . "\n\n");
            self::respond(array("error" => $message, "code" => $code));
        }
        
        public static function respond($data) {
            fputs(self::$log, "Response\n" . print_r($data, true) . "\n");
            die(json_encode($data));
        }
    
    }

?>
