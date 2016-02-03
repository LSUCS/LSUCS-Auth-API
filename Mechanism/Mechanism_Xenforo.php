<?php

    class Mechanism_Xenforo extends Mechanism {
    
        private $usermodel;
    
        public function __construct() {
            //Initiate XenForo
            require(ROOT . '/config.php');
            
            // Xenforo root
            $xfDir = $config['xfdir'];

            require $xfDir . '/library/XenForo/Autoloader.php';
            XenForo_Autoloader::getInstance()->setupAutoloader($xfDir. '/library');
            XenForo_Application::initialize($xfDir . '/library', $xfDir);
            XenForo_Application::set('page_start_time', microtime(true));
            
            //Load user model
            $this->userModel = XenForo_Model::create('XenForo_Model_User');
        }
    
        public function validatecredentials($params) {
            //Validate inputs
            if ($this->isInvalid("username", "notnull") || $this->isInvalid("password", "notnull")) Lsucs_Auth::error(2);
            //Check credentials
            $userId = $this->userModel->validateAuthentication($params["username"], $params["password"], $error);
            if ($userId) Lsucs_Auth::respond(true);
            else Lsucs_Auth::respond(false);
        }
        
        public function getuserbyid($params) {
            //Check input validation
            if ($this->isInvalid("userid", "notnull")) Lsucs_Auth::error(2);
            if ($this->isInvalid("userid", "int")) Lsucs_Auth::error(4);
            
            //Get user, error if not exist
            $user = $this->userModel->getUserById($params['userid']);
            if (!$user) Lsucs_Auth::error(3);
            
            Lsucs_Auth::respond($this->getUserObjFromXenUser($user));
        }
        
        public function getuserbyusername($params) {
            //Check validation
            if ($this->isInvalid("username", "notnull")) Lsucs_Auth::error(2);
        
            //Get users, error if none matched
            $users = $this->userModel->getUsers(array("username" => $params["username"]));
            if (!$users) Lsucs_Auth::error(3);
            
            foreach ($users as $user) {
                if (strtolower($user["username"]) == strtolower($params["username"])) Lsucs_Auth::respond($this->getUserObjFromXenUser($user));
            }
            
            //No direct match found
            Lsucs_Auth::error(3);
        }
        
        public function getusersbyusername($params) {
            //Check validation
            if ($this->isInvalid("username", "notnull")) Lsucs_Auth::error(2);
        
            //Get users, error if none matched
            $users = $this->userModel->getUsers(array("username" => $params["username"]));
            if (!$users) Lsucs_Auth::error(3);
            
            $out = array();
            foreach ($users as $user) $out[] = $this->getUserObjFromXenUser($user);
            
            Lsucs_Auth::respond($out);
        }
        
        public function checkfol($params) {
            include 'config.php';
            
            //Get user
            $user = $this->userModel->getUserById($params['userid']);
            if (!$user) Lsucs_Auth::error(3);
            
            //If not in group, add
            if (!$this->userModel->isMemberOfUserGroup($user, $config['fol_group'], true) && !$this->userModel->isMemberOfUserGroup($user, $config['member_group'], true)) {
                $writer = XenForo_DataWriter::create('XenForo_DataWriter_User');
                $writer->setExistingData($user["user_id"]);
                $groups = explode(",", $user["secondary_group_ids"]);
                $groups[] = $config['fol_group'];
                $writer->setSecondaryGroups($groups);
                $writer->setOption(XenForo_DataWriter_User::OPTION_ADMIN_EDIT, true);
                $writer->save();
            }
        }
        
        private function getUserObjFromXenUser($xenuser) {
            $user = new User();
            
            $user->userid = $xenuser["user_id"];
            $user->username = $xenuser["username"];
            $user->email = $xenuser["email"];
            $user->admin = $xenuser["is_admin"];
            $user->moderator = $xenuser["is_moderator"];
            
            //Groups
            $user->groups = (array)$xenuser["user_group_id"];
            foreach (explode(",", $xenuser["secondary_group_ids"]) as $group) {
                if ($group != "") $user->groups[] = (int)$group;
            }
            
            //Avatar
            if ($xenuser["gravatar"] != "") $user->avatar = XenForo_Template_Helper_Core::getAvatarUrl($xenuser, "l");
            else if ($xenuser["avatar_date"] != "") $user->avatar = "http://lsucs.org.uk/" . XenForo_Template_Helper_Core::getAvatarUrl($xenuser, "l", "content");
            else $user->avatar = "http://lsucs.org.uk/" . XenForo_Template_Helper_Core::getAvatarUrl(array(), "l", "default");
            
            return $user;
        }
    
    }

?>
