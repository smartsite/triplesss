<?php
namespace  Triplesss\collection;

class Collection implements interfaceCollection {
    
    Public $id = 0;
    Public $items = [];
      
    
    function __construct() {
       
    }

    function setId($id) {
        $this->id = $id;
    }

    function getId() :Int {
        return $this->id;
    }

    function addItem($item) {
        array_push($items, $item);
    }

    function removeItem($id) {
        unset($this->items[$id]);
    }


}