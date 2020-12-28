<?php

$post_range = [0,6];
$feed->setPostRange($post_range);
$feed->sortBy("date, desc");
$posts = $feed->getPosts();
$posts = [];

$markup = '';
$like_button = '<button class="post-button like"><i class="material-icons">favorite</i></button><span class="post-likes">2</span>';
$comment_button = '<button class="post-button comment"><i class="material-icons">comment</i></button><span class="post-comments">3</span>';


foreach($posts as $post) {
       
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
   
}

echo $markup;


?>