<?php
namespace Triplesss\message;

use Triplesss\repository\Repository;
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