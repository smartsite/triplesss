<?php
namespace Triplesss\users;

use \Triplesss\filter\Filter;
use Triplesss\repository\Repository as Repository;

class Users {
    
    Public $id = 0;
    Public $filter;
    Public $user_id = -1;
    
    
    function __construct() {
        /**
         *  Use a default filter to limit results
         */

        $this->repository = new Repository();
        $filter = new Filter();
        $filter->setType('range');
        $filter->setRange(0,100);
    }

    public function getUsers() {
        
        $filter = $this->filter;
        $users =  $this->repository->getUsers($filter, true);
        $this->users = $users;
        return $users;
    }

    public function setFilter(Filter $filter) {        
        /**
         *  Custom filters let you sort by first name, last name etc.
         */
        
        $this->filter = $filter;
    }

    public function setUserId(Int $user_id) {
        $this->user_id = $user_id;
    }

    public function getUserId() {
        return $this->user_id;
    }
}