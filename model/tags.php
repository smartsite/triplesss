<?php
namespace Triplesss\tag;

use Triplesss\post\Post;

class Tag {
    
    Public $keys = [];
    Public $post = null;
       
    function __construct() {
      
    }

    public function add(String $key) : Bool {
        if(in_array($key, $this->keys)) {
            array_push($this->keys, $key);
            return true;
        } else {
            return false;
        }       
    }

    public function remove(String $key) {
       unset($this->keys[$key]);
    }

    public function getAll() {
        return $this->keys;
    }

    public function setPost(Post $post) {
        $this->post = $post;
    }

}    