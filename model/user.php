<?php
namespace  Triplesss\user;

use \Triplesss\connection\Connection;
use \Triplesss\notification\Notification;
use \Triplesss\repository\Repository;
use \Triplesss\filter\Filter;
use \Triplesss\error\Error;

class User {
    
    public $username = '';
    public $filter;
    public $repository;
    public $userid;
    public $session;
    
    function __construct() {
        $this->repository = new Repository();
        $this->error = new Error();
        return $this;
    }

    public function setName($name) {
        $this->username = $name;
    }

    public function getName() :String {
        return $this->username;
    }

    public function addBuddy(User $user) {
        /**
         *   An actual buddy         
         **/

        $this->addConnection($user, 1);
    }

    public function setFilter(Filter $filter) {
        $this->filter = $filter;
    }

    public function checkUserName(String $username='') { 
        $repository = $this->repository;
        return $repository->checkUserName($username);
    }

    public function add(String $username='') {
        $repository = $this->repository;
        if($username == '') {
            $username = $this->username;
            if(!$this->username || $this->username == '') {
                $this->error->setMessage('Empty username');
                $this->error->setCode(61);
                return $this->error;  
            }
        }
       
        $taken = $this->checkUserName($username);
        if(!$taken) {
            $this->username = $username;
            return $repository->addUser($username);
        } else {
            $this->error->setMessage('Username exists');
            $this->error->setCode(60);
            return $this->error;  
        }
    }

    public function delete() {
        $repository = $this->repository;
        $user = $this->user;
        return $repository->deleteUser($user);
    }

    public function update(Array $details) {
        $repository = $this->repository;
        return $repository->updateUser($details);
    }

    public function sendRegisterLink(String $username, String $from, String $reply) {
        $repository = $this->repository;
        $repository->generateRegisterLink($username, $from, $reply);
    }

    private function addConnection(User $user, $type) {
         /**
         *   Some other connection         
         **/
        
        $connection = new Connection($type);
        $connection->connect($this, $user, 1);
    }

    public function verify(String $key) {
        // user clicked on the register link 
        $repository = $this->repository;
        $u = $repository->verifyUser($key);
        if($u) {
            $this->username = $u['user_name'];
            $this->userid = $u['id'];
            return true;
        } else {
            return false;
        }
    }

    public function getUserId() {
        $repository = $this->repository;
        return $repository->getUserId($this->username);
    }

    public function setUserId(Int $userid) {
        $this->userid = $userid;
    }

    public function login(String $username, String $password) {
        $repository = $this->repository;
        return $repository->userLogin($username, $password);
    }

    public function logout() {
        $repository = $this->repository;
        return $repository->userLogout($this);
    }

    public function getSession() {
        if($this->session) {
            return $this->session;
        } else {
            return false;
        }        
    }

    public function getFeeds() :Array {
        // Get the list of feeds that belong to this user
        $repository = $this->repository;
        return  $repository->getUserFeeds($this->userid);
    }

    /*
    private function generateRegisterLink() {
        $repository = $this->repository;
        return $repository->userRegLink($this->username);
    }
    */
}