<?php

namespace Triplesss\auth;

require '../model/repository.php';

use Triplesss\repository\Repository;

class Auth {       

    private $failed_logins;
    public $repository;
   
    public function __construct() {
        $this->failed_logins = 0; 
        $this->repository = new Repository();
    }
                
    function login(String $username, String $password){
        $repository = $this->repository;
        return $repository->userLogin($username, $password);
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