<?php
namespace  Triplesss\user;

use \Triplesss\connection\Connection;
use \Triplesss\notification\Notification;

class User {
    
    public $name = '';
    
    function __construct() {
        
    }

    function setName($name) {
        $this->name = $name;
    }

    function getName() :String {
        return $this->name;
    }

    public function addBuddy(User $user) {
        /**
         *   An actual  freind         
         **/

        $this->addConnection($user, 1);
    }

    private function addConnection(User $user, $type) {
         /**
         *   Some other connection         
         **/
        
        $connection = new Connection($type);
        $connection->connect($this, $user, 1);
    }
}