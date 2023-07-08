<?php

require '../model/auth.php';
require '../model/repository.php';


/**
 *   A single post object rendered as HTML ( only used in non-AJAX / non-JS framework front ends)
 *     
 */


header('Content-Type: application/json');


if(isset($_GET)) {   
    $po = $_GET;    
} else {
    $content = trim(file_get_contents("php://input"));
    $po = json_decode($content, true);    
}

extract($po);

$uniq = uniqid();
$markup.= '<div class="post '.$uniq.'">';   

$markup.=  '    <div class="options vivify hide"><span data-action="tag">Tag</span><span data-action="report">Report</span><span data-action="hide">Hide</span><span  data-action="delete">Delete</span></div>';
array_map(function($c) use ($markup){
    if($c['content_type'] == 'image') {
        $image = $c['path'].'/'.$c['link'];
        $markup.=  '     <div class="postcontent post-image"><img src="'.$image.'" /></div>';    
    }

    if($c['content_type'] == 'text') {
        $text = $c['content'];
        $markup.=  '     <div class="postcontent post-text"><span>'.$text.'</span></div>';    
    }

}, $post); 



$markup.=  '     <div class="poster">'.$post[0]['owner'].'<span class="reactions">'.$comment_button.'&nbsp;'.$like_button.'</span></div>';
$markup.=  '</div>';

$markup.=  '    <div class="tagbox vivify '. $uniq .' lowlight">';
$markup.=  '         <p>Add tags</p>';
$markup.=  '         <input type="text" />';
$markup.=  '     </div>';

$markup.=  '    <div class="reportbox vivify ' . $uniq . ' lowlight">';
$markup.=  '         <p>Report this image for </p>';
$markup.=  '         <input type="radio" name="report" value="nudity"><label>nudity</label><br />';
$markup.=  '         <input type="radio" name="report" value="graphic"><label>graphic / violent</label><br />';
$markup.=  '         <input type="radio" name="report" value="racist"><label>racism</label><br />';
$markup.=  '         <input type="radio" name="report" value="threat"><label>threats / inciting violence</label><br />';
$markup.=  '         <input type="radio" name="report" value="spam"><label>spam / marketing</label><br />';
$markup.=  '         <button onClick="sendReport(this)">Send report</button>';
$markup.=  '     </div>';

echo $markup;

?>