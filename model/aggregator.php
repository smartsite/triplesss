<?php
namespace  Triplesss\collection;
use Triplesss\feed\Feed;

class Aggregator extends Collection {
    
    Public $id = 0;
    Public $feeds = [];
    
    
    function __construct() {
       
    }

    function setId(Int $id) {
        $this->id = $id;
    }

    function getId() :Int {
        return $this->id;
    }

    function addFeed(Feed $feed) {
        array_push($feeds, $feed);
    }

    function removeFeed(Int $id) {
        unset($this->feeds[$id]);
    }


}