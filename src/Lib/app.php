<?php

namespace Triplesss\App;

use stdClass;
use Triplesss\Lib\Proxy;
use Triplesss\Lib\Request;
use Triplesss\Lib\Response;
use Triplesss\Lib\Router;
use ReallySimpleJWT\Token as Token;

require "proxy.php";

class App
{       

    public function __construct($params = []) {       
    }

    public static function run()
    {         
        Router::get('/api/', function (Request $req, Response $res) {
            echo "Triplesss! API v1.010";
        }); 
        
        Router::get('/api/checkuser/([a-z0-9]*)', function (Request $req, Response $res) {            
            echo Proxy::Get("checkuser.php?username=".$req->params[0]);             
        });        

        Router::post('/api/login', function (Request $req, Response $res ) {  
            echo Proxy::Post("login.php", $req->getBody()); 
        }); 

        // (^[\w-]*\.[\w-]*\.[\w-]*$)        
        Router::get('/api/logged_in/([\w-]*\.[\w-]*\.[\w-]*)', function (Request $req, Response $res) {            
            $logged_in = Proxy::Get("logged_in.php?token=".$req->params[0]); 
            echo  $logged_in;           
        });

        Router::post('/api/register', function (Request $req, Response $res ) {  
            echo Proxy::Post("register.php", $req->getBody()); 
        }); 

        Router::get('/api/verify/([a-z0-9]*)', function (Request $req, Response $res) {            
            echo Proxy::Get("verify.php?key=".$req->params[0]);             
        });       
        
        Router::get('/api/feed/([0-9]*)/offset/([0-9]*)/count/([0-9]*)', function (Request $req, Response $res) {            
            echo Proxy::Get("feed.php?feed_id=".$req->params[0]."&offset=".$req->params[1]."&count=".$req->params[2]);             
        });

        Router::get('/api/user/([0-9]*)/feeds', function (Request $req, Response $res) {            
            echo Proxy::Get("feeds.php?userid=".$req->params[0]);             
        });

        Router::get('/api/user/([0-9]*)/aggregator/offset/([0-9]*)/count/([0-9]*)', function (Request $req, Response $res) {            
            echo Proxy::Get("aggregator.php?userid=".$req->params[0]."&offset=".$req->params[1]."&length=".$req->params[2]);             
        });
       
        Router::get('/api/user/([0-9]*)/profile', function (Request $req, Response $res) {            
            echo Proxy::Get("profile.php?userid=".$req->params[0]);    
        });

        Router::get('/api/user/([0-9]*)/value/([a-z]*)', function (Request $req, Response $res) {            
            echo Proxy::Get("user_value.php?username".$req->params[0]);    
        });

        Router::post('/api/user/value', function (Request $req, Response $res ) {  
            echo Proxy::Post("user_value.php", $req->getBody()); 
        });

        Router::get('/api/user/search/([0-9]*)', function (Request $req, Response $res) {            
            echo Proxy::Get("search_user.php?userid=".$req->params[0]);    
        });

        Router::get('/api/user/search/([a-z]*)', function (Request $req, Response $res) {            
            echo Proxy::Get("search_user.php?username".$req->params[0]);    
        });

        Router::get('/api/user/search/([0-9]*)/([a-z]*)', function (Request $req, Response $res) {            
            echo Proxy::Get("search_user.php?userid=".$req->params[0]."&username=".$req->params[1]);    
        });

        Router::post('/api/user/password/reset', function (Request $req, Response $res ) {  
            echo Proxy::Post("reset_password.php", $req->getBody()); 
        });

        Router::post('/api/user/password/resetlink', function (Request $req, Response $res ) {  
            echo Proxy::Post("reset_link.php", $req->getBody()); 
        });

        Router::get('/api/user/([0-9]*)/password/token/([0-9]*)/([a-z]*)', function (Request $req, Response $res) {            
            echo Proxy::Get("reset_token.php?userid=".$req->params[0]."&token=".$req->params[1]);    
        });   

        Router::get('/api/user/connections/([0-9]*)', function (Request $req, Response $res) {            
            echo Proxy::Get("connections.php?userid=".$req->params[0]);    
        });

        Router::get('/api/user/connection/([0-9]*)/([0-9]*)', function (Request $req, Response $res) {            
            echo Proxy::Get("connection.php?to=".$req->params[1]."&from=".$req->params[0]);    
        });

        Router::get('/api/user/([0-9]*)/notifications', function (Request $req, Response $res) {            
            echo Proxy::Get("notifications.php?userid=".$req->params[0]);    
        });

        Router::get('/api/user/([0-9]*)/reactions', function (Request $req, Response $res) {            
            echo Proxy::Get("reactions.php?user_id=".$req->params[0]);    
        });  

        Router::post('/api/image', function (Request $req, Response $res ) {  
            echo Proxy::Post("image.php", $req->getBody()); 
        });  
       
        Router::post('/api/post', function (Request $req, Response $res ) {  
            echo Proxy::Post("post.php", $req->getBody()); 
        });  

        Router::post('/api/post/edit', function (Request $req, Response $res ) {  
            echo Proxy::Post("post_edit.php", $req->getBody()); 
        }); 

        Router::post('/api/post/comment', function (Request $req, Response $res ) {  
            echo Proxy::Post("comment.php", $req->getBody()); 
        });
      
        Router::get('/api/post/([0-9]*)/([a-z]*)/comments', function (Request $req, Response $res) {            
            echo Proxy::Get("comments.php?post_id=".$req->params[0]);    
        });       

        Router::post('/api/post/reaction', function (Request $req, Response $res ) {  
            echo Proxy::Post("reaction.php", $req->getBody()); 
        });        
       
        Router::post('/api/post/visibility', function (Request $req, Response $res ) {  
            echo Proxy::Post("post_visibility.php", $req->getBody()); 
        });  

        Router::post('/api/post/tags', function (Request $req, Response $res ) {  
            echo Proxy::Post("post_tags.php", $req->getBody()); 
        }); 
        
        Router::post('/api/member', function (Request $req, Response $res ) {  
            echo Proxy::Post("member.php", $req->getBody()); 
        });

        Router::get('/api/member/([0-9]*)', function (Request $req, Response $res) {            
            echo Proxy::Get("member.php?member_id=".$req->params[0]);    
        }); 


        Router::get('/api/member/user/([0-9]*)', function (Request $req, Response $res) {            
            echo Proxy::Get("member.php?user_id=".$req->params[0]);    
        });  

       
       
       
        
        
       // return fetch('/Triplesss/api/post_tags.php', {
      
    }
}