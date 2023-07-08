<?php
namespace Triplesss\user;

class Subscriber extends User {
    
    Public $id = 0;
    
    
    function __construct($subscriber) {
        $this->Subscriber = $subscriber;
    }

    function setId($id) {
        $this->id = $id;
    }

    function getId() :Int {
        return $this->id;
    }
}