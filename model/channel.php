<?php

/**
 * 
 *  A channel is similar to a feed, except it doesn't belong to a specic user ( closer to a Group in function ) 
 * 
 */

namespace  Triplesss\channel;

class Channel {
    
    Public $id = 0;
    public $subscribers = [];
    
    
    function __construct($subscriber) {
        $this->Subscriber = $subscriber;
    }

    function setId($id) {
        $this->id = $id;
    }

    function getId() :Int {
        return $this->id;
    }

    function addSubscriber($id) {
        array_push($this->subscribers, $id);            
    }

    function removeSubscriber($id) {
        unset($this->subscribers[$id]);
    }
}