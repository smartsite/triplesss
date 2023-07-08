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
   
    public function __construct($tok) {
        $this->failed_logins = 0; 
        $this->token = $tok;
        $this->repository = new Repository();
    }

    public function createToken($payload, $secret) {
        $tok = $this->tok::customPayload($payload, $secret);
        return $tok;
    }
                
    function login(String $username, String $password){
        $repository = $this->repository;  
        $repository->token = $this->token;     
        return $repository->userLogin($username, $password, true); // last arg is hashed / not hashed
    }

    function logout(){
        $repository = $this->repository;
        return $repository->userLogout();
    }    
    
    function isLoggedIn($token){
        $repository = $this->repository;
        //$tok = $this->token;
        $repository->token = $this->token;    
        return $repository->isUserLoggedIn($token);
    }        
       
    function validSessionId($sid)
    {
        return preg_match('/^[-,a-zA-Z0-9]{1,128}$/', $sid) > 0;
    }



}

?>