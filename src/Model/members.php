<?php

/**
 *  Do stuff with members
 * 
 */

namespace  Triplesss\users;

use \Triplesss\notification\Notification;
use \Triplesss\repository\Repository;
use \Triplesss\filter\Filter;
use \Triplesss\error\Error;


class Members extends Users { 
    
    Public $filter;
    Protected $repository;

    function __construct() {
        $this->repository = new Repository();
    }
    
    public function getAll() {
        return $this->repository->getMembers($this);
    }

    public function getFilter() {
        return $this->filter;
    }

    public function setFilter(Filter $filter) {
        $this->filter = $filter;
    }
}