<?php
namespace  Triplesss\filter;

use Triplesss\tag\Tag as Tag;

/* filter for things like posts, e.g date, tags */

class Filter { 

    Public $type;
    Public $tags;
    
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


}