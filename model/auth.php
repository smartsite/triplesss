<?php

// TODO: move DB calls to repository class


namespace Triplesss\auth;

use Triplesss\db\DB;

class Auth {       

    private $failed_logins = 0;
    private $db;

    public function __construct(Db $db) {
        $this->failed_logins = 0;
        $this->db = $db;
    }

    private function userLookUp(String $usr){
            
        $db = $this->db;     
        $result = $db->query("SELECT * FROM user WHERE user_name = '".$usr."'");
        return $db->fetchAssoc( $result );
    }


                
    function login(String $usr, String $pwd){
        // return false if incorrect credentials
        $error = Array();
        $db = $this->db;     
        $userObj = $this->userLookUp($usr);

        if($userObj){
            //var_dump($row);
            if(strtoupper($userObj['password']) == strtoupper(hash ("sha256", $pwd)) ){
                // winner, winner chicken dinner!
               
                $session_id =  $this->getSession();
                $_SESSION['user'] = $usr;
                $expiry = time() + (1 * 1 * 5 * 60);  // 1 days x 24 hours x 60 mins x 60 secs ( 5 mins for testing !! )
                $_SESSION['expires'] =  $expiry;
                setcookie("userID", $userObj['id'],  $expiry );
                setcookie("userName", $usr,  $expiry );
                $db->query("DELETE FROM session WHERE user_id='".$userObj['id']."'"); // clean up an old sessions for this user
                $db->query("INSERT INTO session VALUES('".$session_id."', '".$userObj['id']."', '".$expiry."')");
                $error['message'] = "logged in";
                $error['user'] = $usr;
                $error['success'] = "true";
                $this->failed_logins = 0;
                
            }else{
                //echo "THat password's wrong, baby!";
                $error['message'] = "Incorrect password";
                $error['success'] = "false";
                $this->failed_logins++;
            }
        }else{
            // no result... that's bad!
            $error['message'] = "Unknown user";
            $error['success'] = "false";
        }
        return $error;
    }

    function logout($usr){
        //session_start();
        //setcookie(session_name(), "", time() - 3600); //send browser command remove sid from cookie
        //session_destroy(); //remove sid-login from server storage
        //session_write_close();
        $userObj = $this->userLookUp($usr);
        $this->db->query("DELETE FROM session WHERE user_id='".$userObj['id']."'"); // clean up an old sessions for this user
    }
    
    
    function isLoggedIn(){
        // first check cookie
        $loggedIn = false;
        $db = $this->db;     
        if(!isset( $_COOKIE['userID'])){
            // if no cookie, then check DB for non-expired session
            if(isset($_COOKIE['PHPSESSID'])){
                $sid = $_COOKIE['PHPSESSID'];
                $result = $db->query("SELECT * FROM session WHERE session_id = '".$sid."'");
                $db_session = $db->fetchAssoc( $result );
                if( $db_session['expires'] > time()){
                    //echo "Is good!";
                    $loggedIn = true;
                }
            }    
        }else{
            $loggedIn = true;
        }
        return $loggedIn;
    } 

       
       
    function getSession(){
        $sessionId = null;
        
        if(!session_id()){
            session_start();
            session_regenerate_id();
            $sessionId = session_id();
        }
        return $sessionId;
    }

    function clearSession(){
        session_unset(); 
        session_destroy(); 
    }
    
    function validSessionId($sid)
    {
        return preg_match('/^[-,a-zA-Z0-9]{1,128}$/', $sid) > 0;
    }

}

function getPostedData(){
    // Accepts form data or json
    $contentType = isset($_SERVER["CONTENT_TYPE"]) ? trim($_SERVER["CONTENT_TYPE"]) : '';
   
    $postedData = [];
    if ($contentType === "application/json"){
        $content = trim(file_get_contents("php://input"));
       
        $postedData = json_decode($content, true);
    } elseif ($contentType === "application/x-www-form-urlencoded" || $contentType === "multipart/form-data") {
        $postedData = $_POST;
    }
    return $postedData;
}

?>