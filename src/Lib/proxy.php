<?php 

namespace Triplesss\Lib;

class Proxy
{   
      
    public function __construct($params = [])
    {
        
    }    


    public static function Get($endpoint) {           
        $ch = curl_init();
        $base = self::Api_base();
        curl_setopt($ch, CURLOPT_URL, $base."/".$endpoint);      
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);        
        return curl_exec($ch);      
    }

    public static function Post($endpoint, $body) {
        $ch = curl_init();
        $base = self::Api_base();
        curl_setopt($ch, CURLOPT_URL, $base."/".$endpoint);    
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        return curl_exec($ch); 
    }

    public function Set_Base($base_url) {
       $this->base_url = $base_url; 
    }

    public function Set_Host($host) {
        $this->host = $host; 
    }

    private static function Api_base() {
        $host = $_SERVER['HTTP_HOST'];
        $base = str_replace($_SERVER['DOCUMENT_ROOT'], '', dirname(__FILE__));
        $base = str_replace("/lib", "/api", $base);
        return $host.$base;
    }   
}