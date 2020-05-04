<?php
namespace  Triplesss\reaction;

use Triplesss\user\User;

class Reaction {
    
    public $level = 0;
    public $user = null;
    
    function __construct(Int $level, User $user) {
        $this->level = $level;
        $this->user = $user;
    }

    public function getTypes() {
        return [
           -3 => 'angry',
           -2 => 'sad',
           -1 => 'dislike',
            0 => 'none',
            1 => 'meh',    
            2 => 'like',
            3 => 'love',
            666 => 'satan loves you',
        ];
    }

    public function get() {
        return $this;
    }

    public function getUser() {
        return $this->user;
    }

}   