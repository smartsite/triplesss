<?php
namespace  Triplesss\collection;
use Triplesss\feed\Feed;

class Aggregator extends Collection {
    
    Static $id = 0;
    Static $feeds = [];
    
    
    function __construct($aggregator) {
        $this->Aggregator = $aggregator;
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