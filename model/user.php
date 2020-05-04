<?php
namespace  Triplesss\user;

use \Triplesss\connection\Connection;
use \Triplesss\notification\interfaceNotification;


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
        $this->addConnection($user, 1);
    }

    private function addConnection(User $user, $type) {
        $connection = new Connection($type);
        $connection->connect($this, $user, 1);
    }
}