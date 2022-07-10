<?php
namespace Triplesss\message;

use Triplesss\post\Post;

class Message extends Post {
    
    Public $from;
    Public $to;
    Public $message;
    Public $reply;

    function __construct() {       
        $this->repository = new Repository();
        return $this;
    }
}    