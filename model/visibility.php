<?php
namespace  Triplesss\visibility;

use Triplesss\collection\Collection;
use Triplesss\repository\Repository as Repository;

class Visibility {
    
    Private $repository;
    Static $level = 0;
    Static $levels = [];
          
    
    function __construct($feed) {       
        $this->repository = new Repository();
        $this->Feed = $feed;
        $this->levels = $this->repository->getVisibilities();
    }

    function setLevel(Int $level) {
        $this->level = $level;
    }

    function getLevel() :Int {
        return $this->level;
    }

    function getAccessList() {
        // return array of users who can see this asset based on the level

        // 'group' is a special collection with customised members
    }

    public function getLevels() {
      return $this->levels;
    }

   


}