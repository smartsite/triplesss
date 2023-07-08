<?php
namespace Triplesss\conversation;

use Triplesss\repository\Repository;
use Triplesss\user\User;
use Triplesss\message\Message;

class Conversation { 


    Public $participants = [];
   

    function __construct() {       
        $this->repository = new Repository();
        return $this;
    }

    Public function addUser(User $user) {
        array_push($this->participants, $user);
    }

    Public function removeUser(User $user) {
        $key = array_search($user, $this->participants);
        unset($participants[$key]);
    }
}