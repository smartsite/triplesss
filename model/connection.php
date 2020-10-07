<?php
namespace  Triplesss\connection;
use \Triplesss\user\User;

class Connection  {
    
   
    Public $type; 
    Public $id;
    Public $from;
    Public $to;
        
    public function __construct($id) {
        $this->id = $id;
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
    }

    public function getTypes() {
        return [
            0 =>  'null',
            1 =>  'freind',
            2 =>  'follower'        
        ];
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
