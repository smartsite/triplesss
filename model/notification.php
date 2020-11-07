<?php
namespace  Triplesss\notification;
use Triplesss\user\User;
use Triplesss\repository\Repository;

class Notification {
    
    Public $type = null;
    Public $typeid = 0;
    Public $message = '';
    Public $id = -1;
    Public $repository;
    Public $from_user;
    Public $to_user;
    
    public function __construct(User $user) {
        $this->to_user = $user;
        // By default, all notifications come system unless it's overridden
        $system = new User();
        $system->setUserId(0);
        $this->from_user = $system;
        $this->repository = new Repository();
    }

    public function setType(String $type) {
        $this->type = $type;
    }

    public function getType() :String {
        return $this->type;
    }

    public function setFromUser(User $user) {
        $this->from_user = $user;
    }

    public function getFromUser() :User {
        return $this->from_user;
    }

    public function notify() {
        $this->message = $this->getTemplate();
        $this->repository->setNotification($this);       
    }

    public function setMessage(String $message) {
        $this->message = $message;            
    }

    public function getMessage() :String {
        //$this->message =  $this->repository->getNotification($this);    
        return $this->message;        
    }

    public function getTypes() :Array {
        return [
            0 =>  'null',
            1 =>  'status',
            2 =>  'post',
            3 =>  'comment',
            4 =>  'reply',
            5 =>  'reaction',
            6 =>  'request',
            7 =>  'accept'          
        ];
    }

    public function getTemplate() :String {
        $type_idx = array_search($this->type, $this->getTypes());
        $this->typeid = $type_idx;
        $message = $this->message;
       
        $username = $this->to_user->getName();
        
        if($type_idx == 7 || $type_idx == 6 || $type_idx == 3 || $type_idx == 5) {
            $username = $this->from_user->getName();
        }
        
        $templates = [
            0 => 'system message: '.$message,
            1 => $username.' posted a status update',
            2 => $username.' posted something new',
            3 => $username.' commented on your post',
            4 => $username.' replied to your comment',
            5 => $username.' reacted to your post',
            6 => $username.' sent you a contact request',
            7 => $username.' accepted your contact request'
        ];
        return $templates[$type_idx];
    }
}