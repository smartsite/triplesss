<?php

$tpl = file_get_contents('templates/email/welcome.html');
$csv = file('templates/email/sendlist.csv');

//var_dump($csv);
$titles = explode(';', $csv[0]);
array_shift($csv);

//var_dump($titles);


array_map(function($line) use($tpl){
    $cols = explode(';', $line);
    $firstname = $cols[0];
    $username = $cols[1];
    $email = $cols[2];
    //echo($firstname.', '.$username.', '.$email)."<br>";
    sendMail($firstname, $username, $email, $tpl);
}, $csv);


function sendMail($firstname, $username, $email, $tpl) {
    $from = 'VIP.surfsouthoz<admin@surfsouthoz.com>';
    $subject = 'Welcome to surfsouthoz VIP!';
    $repy = 'admin@surfsouthoz.com';

    $msg = str_replace('%%username%%', $username, $tpl);
    $msg = str_replace('%%firstname%%', $firstname, $msg);
    
    $headers = "MIME-Version: 1.0" . "\r\n"; 
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n"; 
    $headers .= 'From: '.$from. "\r\n" .
                        'Reply-To:' .$reply. "\r\n" .
                        'X-Mailer: PHP/' . phpversion();
            
    $m = mail($email, $subject, $msg, $headers);
    if($m) {
    $sent = ['status' => 'sent', 'email' => $email];  
    } else {
    $sent = ['status' => 'error', 'email' => $email];  
    }

    var_dump($sent);
}

/*
$firstname = "Boris";
$username = "Boris99";
$email = 'smartsite99@gmail.com';

$msg = str_replace('%%username%%', $username, $tpl);
$msg = str_replace('%%firstname%%', $firstname, $msg);

$from = 'VIP.surfsouthoz<admin@surfsouthoz.com>';
$subject = 'Welcome to surfsouthoz VIP!';

//$msg = 'We received a password reset request for your surfsouthoz account. Click or tap on this link '.$link.' to create a new password.';
$headers = "MIME-Version: 1.0" . "\r\n"; 
$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n"; 
$headers .= 'From: '.$from. "\r\n" .
                    'Reply-To:' .$reply. "\r\n" .
                    'X-Mailer: PHP/' . phpversion();
           
$m = mail($email, $subject, $msg, $headers);
if($m) {
  $sent = ['status' => 'sent', 'email' => $email];  
} else {
  $sent = ['status' => 'error', 'email' => $email];  
}

var_dump($sent);
*/

?>