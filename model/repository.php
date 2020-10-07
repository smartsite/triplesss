<?php
namespace  Triplesss\repository;

require_once('dbsettings.php');
require_once('db.php');
require_once('error.php');

use Triplesss\error\Error as Error;
use Triplesss\feed\Feed as Feed;
use Triplesss\post\Post as Post;
use Triplesss\user\User as User;
use Triplesss\filter\Filter as Filter;
use Triplesss\db\DB as Db;
use Triplesss\content\content as Content;
use Triplesss\visibility\Visibility as Visibility;

/**
 * 
 *   MySQL ORM
 *   
 *   The Repository object is an abstraction of the database layer, seprating query-level
 *   CRUD from the application layer. It can be written to suit different storage systems, 
 *   e.g PostGres, MongoDB or any NoSQL type 
 *   
 *   Create a single instance of the DB class and access all
 *   data objects thtough this class 
 * 
 * */

class Repository {
    
    Public $db;
    Public $error;
       
    function __construct() {
        $this->db = new Db(); 
        $this->error = new Error;       
    }

    Public function imageAdd(String $link, String $path, String $type, String $mime, Int $userId, String $tags='') {
        $db = $this->db;
        $s = 'INSERT INTO `image` SET `link`="'.$link.'", `path`="'.$path.'", `created`="'.date("Y-m-d H:i_s").'", `type`="'.$type.'", `mime_type`="'.$mime.'", `creator_id`='.$userId.', `tags`="'.$tags.'"';
        $r = $db->query($s);
        //$db->freeResult($r);
        if($r) {
            $id = $db->lastInsertedID();
            $s = 'INSERT INTO content SET content_id='.$id.', content_type="image"';
            $in =  $db->query($s);
            return ["id" => $id];
        } else {
            return $db->sql_error();
        }     
    }

    Public function textAdd(String $text, Int $user_id, String $tags) {
        $db = $this->db;
        $text_id = 0;
        $s = 'INSERT INTO `text` SET `creator_id`='.$user_id.', `content`="'.$text.'", `text_id`="'.$text_id.'", `created`="'.date("Y-m-d H:i_s").'", `tags`="'.$tags.'"';
        $r = $db->query($s);
        //$db->freeResult($r);
       
        if($r) {
            $id = $db->lastInsertedID();
            $s = 'INSERT INTO content SET content_id='.$id.', content_type="text"';
            $in =  $db->query($s);
            return ["id" => $id];
        } else {
            return $db->sql_error();
        }  
    }

    

    Public function assetGetById(Int $id, String $asset_type) {
        
        $this->checkAssetType($asset_type);        
        $db = $this->db;
        $s = 'SELECT * FROM '.$asset_type.' WHERE id='.$id;        
        $p = $db->query($s);
        $r = $db->fetchRow($p);
        return $r;       
    }

    Public function getAssetFiltered(String $tags = '', Int $user_id = -1, String $asset_type) {
        
        $this->checkAssetType($asset_type);        
        $db = $this->db;
        // first, count how many tags there are 
       
        $s = 'SELECT * FROM '.$asset_type.' WHERE ';
        $w = $this->tagSelect($tags);

        if($user_id > -1) {
            $w.= ' AND creator_id='.$user_id;
        }        
        $q = $s.$w;
        //echo $q;
        $p = $db->query($q);
        $r = $db->fetchAll($p);
        return $r;
    }

    //Public function getPostById(Int $id) {
    Public function getPostById(String $id) {
       
        $r = [];
        $r =  array_merge($r, $this->getPostAsset('image', $id));
        $r = array_merge($r, $this->getPostAsset('text', $id));  

        $assets = array_map(function($post_item) {
         
            if($post_item['content_type'] == 'text') {
               
               return [
                    'owner' => $post_item['owner'], 
                    'content_type' => $post_item['content_type'], 
                    'text_id' => $post_item['text_id'], 
                    'content' => $post_item['content'], 
                    'tags' =>    $post_item['tags'], 
                    'creator_id' => $post_item['creator_id'],
                    'visibility' =>   $post_item['visibility']                   
                ];               
            }
            
            if($post_item['content_type'] == 'image') {
              
                return [
                    'owner' => $post_item['owner'], 
                    'content_type' => $post_item['content_type'], 
                    'link' => $post_item['link'], 
                    'path' => $post_item['path'], 
                    'tags' =>    $post_item['tags'], 
                    'mime_type' =>    $post_item['mime_type'], 
                    'creator_id' => $post_item['creator_id'],
                    'visibility' =>   $post_item['visibility']                       
                ];
            }          
            
        }, $r);
        return $assets;
    }

    Public function getPostAsset(String $type, String $post_id) {

        if($this->checkAssetType($type)) {
            $db = $this->db;
            $s = 'SELECT * FROM content_post 
                    JOIN post ON content_post.post_id = post.id 
                    JOIN '.$type.' ON '.$type.'.id = content_post.content_id AND content_post.content_type="'.$type.'"   
                    WHERE post.post_id="'.$post_id.'"';            
          
            $p = $db->query($s);
            $r = $db->fetchAll($p);
            return $r; 
        }  
    }

    Public function addPost(Post $post) {
        // gets all the post items and add them to the post table
        // first, insert ths post
        $db = $this->db;
        $owner = $post->getOwner();
        $post_id = $this->newPostId();
        $s = 'INSERT INTO post SET owner='.$owner.', post_id="'.$post_id.'", title=""';
        $db->query($s);
        $p_id = $db->lastInsertedID();
        $items = $post->getItems();

        array_map(function(Content $item) use($db, $p_id) {
            $id = $item->getContentId();
            $content_type = $item->getContentType();
            $qry = 'INSERT INTO content_post SET post_id='.$p_id.', content_id='.$id.', content_type="'.$content_type.'"';
            $r = $db->query($qry);           
        }, $items);  
        
        return $post_id;
    }  

    Public function editPost(Post $post, String $text, Image $image) {
        $db = $this->db;
        $owner = $post->getOwner();
        // basically we just need to do an update on the text in the text table if it's not empty,
        // and update the image if a replacement has been uploaded
    }
    
    Public function addPostToFeed(Post $post, Feed $feed) {
        $db = $this->db;
        //$p_id = $post->getId();
        $p_id = $post->getPostId();
        $f_id = $feed->getId();
        $id = $this->newPostId();
        $visibility = $post->getVisibility()->getLevel();
        $qry = 'INSERT INTO feed_post SET id="'.$id.'", post_id="'.$p_id.'", feed_id='.$f_id.', visibility='.$visibility;
        //echo $qry;
        $p = $db->query($qry);   
        if($p) {
            return $db->lastInsertedID();
        } else {
            return $db->sql_error();
        }
    }

    Public function getFeedPosts(Feed $feed, Filter $filter=null) {
        
        $db = $this->db;
        $s = 'SELECT post_id FROM feed_post WHERE feed_id='.$feed->id;
        if($filter) {
            if($filter->type == 'userid' && $filter->userid) {
                $s = 'SELECT feed_post.post_id FROM feed_post JOIN post ON post.post_id = feed_post.post_id 
                WHERE feed_id='.$feed->id.' AND post.owner='.$filter->userid;
            }           
        }
        $p = $db->query($s);
        $r = $db->fetchAll($p);

        $posts = array_map(function($p){          
            return $this->getPostById($p['post_id']);
        }, $r);
        return $posts;
    }

    
    public function checkUserName(String $username) {
        $db = $this->db;
        $s = 'SELECT user_name FROM user WHERE user_name="'.$username.'"';
        $p = $db->query($s);
        $r = $db->fetchAll($p);
        return count($r) > 0;        
    }

    public function temporaryPassword(Int $length = 10) :String {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $count = mb_strlen($chars);
    
        for ($i = 0, $result = ''; $i < $length; $i++) {
            $index = rand(0, $count - 1);
            $result .= mb_substr($chars, $index, 1);
        }    
        return $result;
    }

    public function addUser(String $username) {
        $db = $this->db;
        $temppwd = $this->temporaryPassword();
        $qry = 'INSERT INTO user SET user_name="'.$username.'", first_name="", last_name="", password="'.$temppwd.'"';
        $p = $db->query($qry); 
        if($p) {
            return $db->lastInsertedID();
        } else {
            return $db->sql_error();
        }  
    }

    public function getUserId(String $username) {
        $db = $this->db;
        $s = 'SELECT id FROM user WHERE user_name="'.$username.'" LIMIT 1';
        $p = $db->query($s);
        $r = $db->fetchAll($p);
        if($r) {
            return $r[0]['id'];
        } else {
            return -1;
        }       
    }

    public function deleteUser(User $user) {
        return true;
    }

    public function updateUser(Array $details) {
        $db = $this->db;
        $username = $details['user_name'];
        $s = 'UPDATE user SET ';
        
        $q = array_map(function($d, $k) {
            return '`'.$k.'`="'.$d.'"';
        }, $details, array_keys($details));
        $s.= implode(',', $q);
        $s.= ' WHERE user_name="'.$username.'"';
        $p = $db->query($s);
        return $p;
    }

    public function verifyUser(String $key) {
        $db = $this->db;
        $s = 'SELECT id, user_name FROM user WHERE reg_link LIKE "%'.$key.'%"';
        $p = $db->query($s);
        $r = $db->fetchAssoc($p);
        if($r) {
            $id = $r['id'];
            $s = 'UPDATE user SET active=1 WHERE id='.$id;
            $p = $db->query($s);
        }
        return $r;
    }

    public function userLogin($username, $password, $hashed = false) {
        $db = $this->db;
        $error = [];
        $userObj = $this->allUserDetails($username);
        if($hashed) {
            $password = hash ("sha256", $password);
        }
        
        if($userObj){
            if(strtoupper($userObj['password']) == strtoupper($password)) {
                // winner, winner chicken dinner!
               
                $session_id =  $this->getSession();
                $_SESSION['username'] = $username;
                $expiry = time() + (1 * 1 * 5 * 60);  // 1 days x 24 hours x 60 mins x 60 secs ( 5 mins for testing !! )
                $_SESSION['expires'] =  $expiry;
                setcookie("userID", $userObj['id'],  $expiry, "/" );
                setcookie("userName", $username,  $expiry, "/" );
                $db->query("DELETE FROM session WHERE user_id='".$userObj['id']."'"); // clean up an old sessions for this user
                $db->query("INSERT INTO session VALUES('".$session_id."', '".$userObj['id']."', '".$expiry."')");
                $error['message'] = "logged in";
                $error['username'] = $username;
                $error['success'] = "true";
                $this->failed_logins = 0;
                
            }else{
                //echo "That password's wrong, baby!";
                $error['message'] = "Incorrect password";
                $error['success'] = "false";
                //$this->failed_logins++;
            }
        }else{
            // no result... that's bad!
            $error['message'] = "Unknown user";
            $error['success'] = "false";
        }
        return $error;
    }

    public function isUserLoggedIn() {
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

    public function userLogout($username) {
        $db = $this->db;
        $s = 'DELETE FROM session WHERE user_id='.$this->getUserId($username);
        $p = $db->query($s);
        if($p) { 
            return true;
        } else {
            return false;
        }
    }

    public function generateRegisterLink($username, $from, $reply) {
        $db = $this->db;
        $key = $this->randomString(12);
        $link = gethostname().'/user_register.php?key='.$key;
        $s = 'UPDATE user SET reg_link="/user_register.php?key='.$key.'" WHERE user_name="'.$username.'"';
        $p = $db->query($s);
        if($p) {
            // send email;
            $this->sendRegEmail($username, $link, $from, $reply);
        }
    }

    

    public function getUsers(Filter $filter, Bool $safe) {
        $db = $this->db;
        if($safe) {
            $s = 'SELECT user_name, first_name, last_name, last_login, is_logged_in, user_level, active FROM users';
        } else {
            $s = 'SELECT * FROM users';
        }
       
        if($filter->getType() == 'range') {
            $range = $filter->getRange();
            $s.= ' LIMIT '.$range[1].' OFFSET '.$range[0]; 
        }
        $p = $db->query($s);
        $r = $db->fetchAll($p);
        return $r;
    }


    public function getVisibilities() {
        $db = $this->db;
        $s = 'SELECT * FROM visibility';
        $p = $db->query($s);
        $r = $db->fetchAll($p);
        return $r;
    }

    private function allUserDetails($username = '', $userid = -1) {
        $db = $this->db;
        $s = 'SELECT * FROM user WHERE ';
        if($username != '') {
            $s.= ' user_name="'.$username.'" ';
        } elseif($userid != -1) {
            $s.= ' id="'.$userid.'" ';
        } else {
            $s.= ' user_name="'.$username.'" AND id="'.$userid.'"';
        }
        $p = $db->query($s);
        $r = $db->fetchAll($p);
        if($r) {
            return $r[0]; 
        } else {
            return false;
        }
    }

    private function tagSelect($tags) {
        $w = '1 = 1 ';
        if($tags != '') {
            $w.= ' AND ';
            $tg = explode(',', $tags);
            $itg = [];
            foreach($tg as $tag) {
               $itg[] = '"'.$tag.'"';
            }
            $intags = implode(',', $itg);

            $ss = [];
            for($i=1; $i<10 + 1; $i++) {
                $qry = 'SUBSTRING_INDEX(SUBSTRING_INDEX(tags, \',\', '.$i.'), \',\', -1) IN ('.$intags.')';
                array_push($ss, $qry);
            }
            $w.= '('.implode(' OR ', $ss).')';
        }
        return $w;
    }

    private function checkAssetType(String $asset_type) {
        if(in_array($asset_type, ['text', 'image'])) {
            return true;     
        }
        
        $this->error->setMessage('Invalid asset type');
        $this->error->setCode(50);
        return $this->error;        
    }

    private function newPostId() :String {
        $postId = bin2hex(openssl_random_pseudo_bytes(32));
        return $postId;
    }

    private function randomString(Int $length) :String {
        $a = array_map(function($c) {
            return chr($c);
        }, array_merge(range(97, 97+25), range(65, 65+25)));        
        
        $k = [];
        
        for($i=0; $i<$length; $i++) {
            $k[] = $a[rand(0, 51)];
        }        
        
        return implode('', $k);
    }

    private function sendRegEmail($username, $link, $from, $reply) {
        $db = $this->db;
        $s = 'SELECT first_name, email FROM user WHERE user_name="'.$username.'" LIMIT 1';
        $p = $db->query($s);
        $r = $db->fetchAll($p);
        if($p) {
            $firstname = $r[0]['first_name'];
            $email = $r[0]['email'];
        }
        $subject = "Confirm your registration";
        $msg = 'Dear '.$firstname.', thanks for registering. Please click or tap on this link</a> to confirm your registration. http://'.$link;
        $headers = 'From: '.$from. "\r\n" .
                    'Reply-To:' .$reply. "\r\n" .
                    'X-Mailer: PHP/' . phpversion();
        return mail($email, $subject, $msg, $headers);
    }


    private function getSession(){
        $sessionId = null;
        
        if(!session_id()){
            session_start();
            session_regenerate_id();
            $sessionId = session_id();
        }
        return $sessionId;
    }

    private function clearSession(){
        session_unset(); 
        session_destroy(); 
    }


}   