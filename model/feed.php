<?php
namespace  Triplesss\feed;

use Triplesss\post\Post;
use Triplesss\filter\Filter;

class Feed {
    
    Public $id = 0;
    Public $posts = [];
      
    
    function __construct() {
       
    }

    function setId($id) {
        $this->id = $id;
    }

    function getId() :Int {
        return $this->id;
    }

    function addPost(Post $post) {
        array_push($this->posts, $post);
    }

    function removePost($id) {
        unset($this->posts[$id]);
    }

    public function getPosts() {
        return $this->posts;
    }

    public function getFilteredPosts(Filter $filter) {
        $filterType = $filter->getType();
        $posts = [];
        if($filterType == 'tag') {
            $tags = $filter->getTags();
            // loop throuhg posts to fid tag matches
        }
        return $posts;
    }

    


}