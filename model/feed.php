<?php
namespace  Triplesss\feed;

use Triplesss\repository\Repository;
use Triplesss\post\Post;

class Feed {
    
    Public $id = 0;
    Public $posts = [];
    Public $repository;
    Public $filter;
    Public $sort_by; 
    Public $range;     
    
    function __construct() {
        $this->repository = new Repository();
        $this->range = [0,100];
        $this->sort_by = 'date_desc';
    }

    function setId($id) {
        $this->id = $id;
    }

    function getId() :Int {
        return $this->id;
    }

    function addPost(Post $post, String $parent='') {
        $this->repository->addPostToFeed($post, $parent, $this);
        array_push($this->posts, $post);
    }

    function removePost($id) {
        unset($this->posts[$id]);
    }

    public function getPosts() {
        $this->posts = $this->repository->getFeedPosts($this);
        $sort_terms = explode("_", $this->sort_by);
        $sort_term = $sort_terms[0];
        $sort_order = "desc";
        $range = $this->range;

        $posts = [];   
        $st = explode(',', $sort_term);    
       
        if($st[1]) {
            $sort_order = trim($st[1]);
        }
        
        //TODO: properly implement sort terms, e.g. popularity, relevance
                  
        if($this->sort_by == "date, desc") {
            $posts = array_reverse($this->posts);
        } else {
            $posts = $this->posts;
        }

        $posts = array_reverse($this->posts);

        $p = array_slice($posts, $range[0], $range[1]);
        $posts = $p;        
       
        return $posts;        
    }

    public function setFilter($filter) {
        $this->filter = $filter;
    }

    public function sortBy($sort_by = "date_desc") {
        $this->sort_by = $sort_by;
    }

    public function setPostRange(Array $range) {
        $this->range = $range;
    }

    public function getFilteredPosts() {
        $filter = $this->filter;
        $filterType = $filter->getType();
        $posts = [];
        if($filterType == 'tag') {
            $tags = $filter->getTags();
            // TODO: loop through posts to find tag matches
        }

        if($filterType == 'userid') {
                      
            $op = $this->repository->getFeedPosts($this, $filter);
            $posts = end($op);
            $this->posts = $posts;         
        }
        return $posts;
    }    

}