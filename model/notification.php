<?php
namespace  Triplesss\notification;
use Triplesss\user\User;

class Notification {
    
    Public $type = null;
    Public $message = '';
    Public $id = -1;
    
    public function __construct(User $user) {
        $this->user = $user;
    }

    public function setType(String $type) {
        $this->type = $type;
    }

    public function getType() :String {
        return $this->type;
    }

    public function setMessage(String $message) {
        $this->message = $message;
    }

    public function getMessage() :String {
        return $this->message;
    }

    public function getTypes() :Array {
        return [
            0 =>  'null',
            1 =>  'user status',
            2 =>  'user post',
            3 =>  'comment',
            4 =>  'reply'         
        ];
    }
}