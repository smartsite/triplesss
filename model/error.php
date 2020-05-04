<?php
namespace  Triplesss\error;

class Error {
    
    Static $message = '';
    Static $code = -1;
    Static $type = null;
    Static $level = 0;
    
    function __construct() {
        
    }

    function setMessage(String $message) {
        $this->message = $message;
    }

    function getMessage() :String {
        return $this->message;
    }

    function setCode(Int $code) {
        $this->code = $code;
    }

    function getCode() :Int {
        return $this->code;
    }

    function setType(String $type) {
        $this->type = $type;
    }

    function getType() :String {
        return $this->type;
    }

    function setLevel(Int $int) {
        $this->type = $int;
    }

    function getLevel() :String {
        return $this->level;
    }


}