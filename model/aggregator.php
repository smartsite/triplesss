<?php

namespace  Triplesss\collection;
use Triplesss\feed\Feed;
use Triplesss\filter\Filter;
use Triplesss\repository\Repository;

class Aggregator extends Collection {
    
    Public $id = 0;
    Public $feeds = [];    
    Public $filter;
    Public $repository;
    Public $userid;
    
    function __construct() {
       $this->repository = new Repository();
    }

    public function addFeed(Feed $feed) {
        array_push($feeds, $feed);
    }

    public function removeFeed(Int $id) {
        unset($this->feeds[$id]);
    }

    public function setUserId(Int $userid) {
        $this->userid = $userid;
    }

    public function getUserId() {
        return $this->userid;
    }

    public function getPosts() {        
        $posts = $this->repository->getAggregatedPosts($this);        
        return $posts;
    }

    public function setFilter(Filter $filter) {
        $this->filter = $filter;
    }

    public function getFilter() {
        return $this->filter;
    }



}