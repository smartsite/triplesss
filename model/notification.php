<?php
namespace  Triplesss\notification;
use Triplesss\user\User;
use Triplesss\repository\Repository;

class Notification {
    
    Public $type = null;
    Public $typeid = 0;
    Public $message = '';
    Public $id = -1;
    Public $repository;
    Public $from_user;
    Public $to_user;
    Public $post_id;
    
    public function __construct(User $user) {
        $this->to_user = $user;
        // By default, all notifications come from the system user unless overridden
        $system = new User();
        $system->setUserId(0);
        $this->from_user = $system;
        $this->repository = new Repository();
    }

    public function setType(String $type) {
        $this->type = $type;
    }

    public function getType() :String {
        return $this->type;
    }

    public function setFromUser(User $user) {
        $this->from_user = $user;
    }

    public function getFromUser() :User {
        return $this->from_user;
    }

    public function notify() {
        $this->message = $this->getTemplate();
        $this->repository->setNotification($this);       
    }

    public function setMessage(String $message) {
        $this->message = $message;            
    }

    public function getMessage() :String {
        //$this->message =  $this->repository->getNotification($this);    
        return $this->message;        
    }

    public function getTypes() :Array {
        return [
            0 =>  'null',
            1 =>  'status',
            2 =>  'post',
            3 =>  'comment',
            4 =>  'reply',
            5 =>  'reaction',
            6 =>  'request',
            7 =>  'accept',
            8 =>  'report_nudity',
            9 =>  'report_graphic',
            10 => 'report_racism',
            11 => 'report_threat',
            12 => 'report_spam',
            13 => 'admin_deleted',
            14 => 'onefour',
            15 => 'bug'              
        ];
    }

    public function getTemplate() :String {
        $type_idx = array_search($this->type, $this->getTypes());
        $this->typeid = $type_idx;
        $message = $this->message;
       
        $username = $this->to_user->getName();
        $user_id = $this->from_user->userid;
        $link = '';

        if($type_idx == 1 || $type_idx == 2 ) {
            $username = $this->to_user->getName();            
            $user_id = $this->to_user->userid;
            $link = '<a href="javascript:userPageView('.$user_id.')">'.$username.'</a>';
        }
        
        if($type_idx == 7 || $type_idx == 6 || $type_idx == 3 || $type_idx == 5 ) {
            $username = $this->from_user->getName();
            $link = '<a href="javascript:userPageView('.$user_id.')">'.$username.'</a>';           
        }

        if($type_idx > 7 && $type_idx < 16 ) {
            // Post reports & bugs
            $username = $this->from_user->getName();
            $link = '<a href="javascript:userPageView('.$user_id.')">'.$username.'</a>';           
        }

        $post_id = $this->post_id;
        $post = '<a href="javascript:gotoPost(&quot;'.$post_id.'&quot;)";>post</a>';
        
        $templates = [
            0 => 'system message: '.$message,
            1 => $link.' posted a status update',
            2 => $link.' '.$post.'ed something new',
            3 => $link.' commented on your '.$post,
            4 => $link.' replied to your comment',
            5 => $link.' reacted to your '.$post,
            6 => $link.' sent you a contact request',
            7 => $link.' accepted your contact request',
            8 => $link.' reported a '.$post.' for nudity',
            9 => $link.' reported a '.$post.' for graphic content',
            10 => $link.' reported a '.$post.' for racist content',
            11 => $link.' reported a '.$post.' for threats, violence or inciting',
            12 => $link.' reported a '.$post.' as spam',
            13 => 'An administrator has hidden your '.$post,
            14 => '',
            15 => $link.' reported an issue - '.$message
        ];
        return $templates[$type_idx];
    }

    public function setPostId(String $post_id) {
        $this->post_id = $post_id;
    }
}