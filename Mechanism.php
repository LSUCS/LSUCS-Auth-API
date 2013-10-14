<?php

    abstract class Mechanism {
    
        private $valid = array();
        private $invalid = array();
    
        final public function handleRequest() {
        
            //Check method exists
            if (!isset($_GET["method"]) || !method_exists($this, $_GET["method"])) {
                Lsucs_Auth::error(1);
            }
            $method = $_GET["method"];
        
            //Validate inputs against running method
            $inputs = $this->getMethodParameters($method);
            if (!is_array($inputs)) $inputs = array();
            $validInputs = array();
            $invalidInputs = array();
            $values = array();
            foreach ($inputs as $name => $filters) {
            
                if (!is_array($filters)) $filters = array($filters);
            
                //Check if it exists
                if (isset($_POST[$name])) {
                    $value = $_POST[$name];
                } else {
                    $value = "";
                }
                
                //If notnull filter doesn't exist and input is null, accept input as valid
                if (!in_array("notnull", $filters) && $value == "") {
                    $validInputs[] = $name;
                    $values[$name] = $value;
                    continue;
                }
                
                //Process filter validation
                $invalid = array();
                foreach ($filters as $filter) {
                    
                    switch ($filter) {
                        
                        case 'notnull':
                            if ($value == "") $invalid[] = 'notnull';
                            break;
                            
                        case 'int':
                            if (!filter_var($value, FILTER_VALIDATE_INT)) $invalid[] = 'int';
                            break;
                            
                        case 'bool':
                            if (filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) === null) $invalid = 'bool';
                            break;
                            
                        case 'email':
                            if (!filter_var($value, FILTER_VALIDATE_EMAIL)) $invalid = 'email';
                            break;
                            
                        case 'ip':
                            if (!filter_var($value, FILTER_VALIDATE_IP)) $invalid = 'ip';
                            break;
                            
                        case 'url':
                            if (!filter_var($value, FILTER_VALIDATE_URL)) $invalid = 'url';
                            break;                            
                        
                    }
                        
                }
                
                //If invalid
                if (count($invalid) > 0) $invalidInputs[$name] = $invalid;
                else $validInputs[] = $name;
                $values[$name] = $value;
                
            }
			
			//Run child page action
            $this->valid = $validInputs;
            $this->invalid = $invalidInputs;
			call_user_func(array($this, $method), $values);
            
        }
        
        final public function isInvalid($parameter, $filter) {
            if (isset($this->invalid[$parameter]) && in_array($filter, $this->invalid[$parameter])) return true;
            return false;
        }
    
        final public function getMethodParameters($method) {
            switch ($method) {
            
                case 'validatecredentials': return array("username" => "notnull", "password" => "notnull"); break;
                
                case 'getuserbyid': return array("userid" => array("int", "notnull")); break;
                
                case 'getuserbyusername': return array("username" => "notnull"); break;
                
                case 'getusersbyusername': return array("username" => "notnull"); break;
                
                case 'checkfol': return array("userid" => array("int", "notnull")); break;
                
            }
        }
        
        
        public abstract function validatecredentials($parameters);
        
        public abstract function getuserbyid($parameters);
        
        public abstract function getuserbyusername($parameters);
        
        public abstract function getusersbyusername($parameters);
        
        public abstract function checkfol($parameters);
        
    
    }

?>