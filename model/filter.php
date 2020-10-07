<?php
namespace  Triplesss\filter;

use Triplesss\tag\Tag as Tag;

/* filter for things like posts, e.g date, tags */

class Filter { 

    /**
     *  Types: tags, limit, range, user  
     * 
     */
    
    Public $type;
    Public $tags;
    Public $args;
    Public $range;
    Public $userid;
    
    function __construct() {
       
    }

    public function getFilter() {
        return $this;
    }

    public function setTags(String $tags) {
        $this->tags = $tags;
    }
    public function getTags() {
        return $this->tags;
    }

    public function setType($type) {
        $this->type = $type;
    }

    public function getType() {
        return $this->type;
    }

    public function setRange($begin, $end) {
        $this->range = [$begin, $end];
    }

    public function getRange() :Array {
        return $this->range;
    }

    public function setUserid($userid) {
        $this->userid = $userid;
    }

    public function getUserid() :Int {
        return $this->userid;
    }


}