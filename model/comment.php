<?php
namespace  Triplesss\post;

use Triplesss\repository\Repository;
use Triplesss\post\Post;
use Triplesss\filter\Filter;
use Triplesss\visibility\Visibility;

/**
 *   A comment is no different from a normal post, it uses the same content
 *   objects ( text, image ) but instead of having a feed as a parent, it has a post as
 *   a parent ( identified by the post_id ).
 * 
 */

class Comment extends Post {
    
    Public $id = 0;   
    Public $post;
    Public $parent_post = 0;  
    Public $filter;
    Public $owner;        
    
    function __construct($owner) {
        $this->repository = new Repository();
        $this->owner = $owner;     
    }

    function addPost(Post $post) {        
        $this->post = $post;
    }

    function getPost() :Post {
        return $this->post;
    }

    function getContent() {
        return $this->content;
    }

    function add() {
        // this does the magic!
        return $this->repository->addPostComment($this);
    }
  
    function setParentId(String $id) {
        $this->parent_post = $id;
    }

    function getParentId() :String {
        return $this->parent_post;
    }

    function addToPost() {
        //$p = $this;
        //$this->repository->addPostToFeed($post);
    }

    function getAll(Int $visibility) {
        return $this->repository->getPostComments($this->parent_post, $visibility);
    }

    function addFilter(Filter $filter) {
        $this->filter = $filter;
    }   
}