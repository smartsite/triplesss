<?php
namespace  Triplesss\text;

class Text {
    
    Public $text = '';
       
    function __construct() {
        //$this->Content = $content;
    }

    function setId($id) {
        $this->id = $id;
    }

    function getId() :Int {
        return $this->id;
    }

    function setText($text) {
        $this->text = $text;
    }

    function getText() {
        return $this->text;
    }
    

}