<?php
namespace  Triplesss\text;
use Triplesss\text\Emoji;

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
        //$emoji = new Emoji();       
        //$this->text =  $emoji->encode($text);
        $this->text = $text;
    }

    function getText() {
        return $this->text;
    }
    

}