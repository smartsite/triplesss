<?php

/**
 *   User authentication, log in, log out
 * 
 */


namespace Triplesss\auth;

require '../model/repository.php';

use Triplesss\repository\Repository;

class Auth {       

    private $failed_logins; // We'll use this later to monitor log in issues
    public $repository;
   
    public function __construct() {
        $this->failed_logins = 0; 
        $this->repository = new Repository();
    }
                
    function login(String $username, String $password){
        $repository = $this->repository;
        return $repository->userLogin($username, $password, true); // last arg is hashed / not hashed
    }

    function logout(){
        $repository = $this->repository;
        return $repository->userLogout();
    }    
    
    function isLoggedIn(){
        $repository = $this->repository;
        return $repository->isUserLoggedIn();
    }        
       
    function validSessionId($sid)
    {
        return preg_match('/^[-,a-zA-Z0-9]{1,128}$/', $sid) > 0;
    }

}

?>