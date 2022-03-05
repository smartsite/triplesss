<?php
namespace Triplesss\message;

use Triplesss\post\Post;

class Message extends Post {
    
    /*
    Public $id = 0;
    Public $postId;
    //Public $userId;
    Public $contentType;
    Public $text = '';
    Public $images = [];
    Public $link = '';
    Public $owner = null; // Alias for userId
    Public $items = [];
    Public $tags = null;
    Public $likes = 0;
    Public $comments = [];
    Public $reactions = [];
    Public $repository;
    Public $visibility;
    */

    Public $from;
    Public $to;
    Public $message;
    Public $reply;

    function __construct() {       
        $this->repository = new Repository();
        return $this;
    }



}    