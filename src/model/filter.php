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
    Public $userid = -1;
    
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

    public function setType(String $type) {
        $this->type = $type;
    }

    public function getType() :String {
        return $this->type;
    }

    public function setRange(Int $begin, Int $end) {
        $this->range = [$begin, $end];
    }

    public function getRange() :Array {
        return $this->range;
    }

    public function setUserid(Int $userid) {
        $this->userid = $userid;
    }

    public function getUserid() :Int {
        return $this->userid;
    }
    
    // these can be partial usernames, e.g. surf could filter "surfsouthoz" and "surfgurrl77"
    public function setUserName($username) {
        $this->username = $username;
    }

    public function getUserName() :string {
        return $this->username;
    }
}