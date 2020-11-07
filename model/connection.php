<?php
namespace  Triplesss\connection;
use \Triplesss\user\User;
use \Triplesss\repository\Repository;

/**
 *   Relationships between users, e.g. follower, friend etc.
 * 
 */

class Connection  {    
   
    Public $type; 
    //Public $id;
    Public $from;
    Public $to;
    Public $repository;
        
    //public function __construct($id) {
    public function __construct() {    
        //$this->id = $id;
        $this->repository = new Repository(); 
    }

    public function setType($type) {
        $this->type = $type;
    }

    public function getType() {
        return $this->type;
    }

    public function connect(User $from, User $to, Int $type) {
        $this->from = $from;
        $this->to = $to;
        $this->type = $type;
        return $this->repository->addConnection($from, $to, $this);
    }

    public function getTypes() {
        $types = $this->repository->getConnectionTypes();
        return $types;
    }

    public function getFrom() {
        return $this->from;
    }

    public function getTo() {
        return $this->to;
    }

    private function setFrom(User $user) {
        $this->from = $user;
    }

    private function setTo(User $user) {
        $this->to = $user;
    }    

}   
