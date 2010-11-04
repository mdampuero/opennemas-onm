<?php
// Fetching the last 5 available videos
$videos = $cm->find('Video', 'contents.content_status=1 and videos.author_name != "otro"', 'ORDER BY created DESC LIMIT 0 , 4');

// For earch video retrieve its information
foreach($videos as $video){
    //$videos_authors[] = new Author($video->fk_user);ç
    //miramos el fuente youtube o vimeo
    if($video->author_name =='vimeo'){
        $url="  http://vimeo.com/api/v2/video/'.$video->videoid.'.php";
        $curl = curl_init( 'http://vimeo.com/api/v2/video/'.$video->videoid.'.php');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_TIMEOUT, 30);
        $return = curl_exec($curl);
        $return = unserialize($return);
        curl_close($curl);
        $video->thumbnail_medium = $return[0]['thumbnail_medium'];
        $video->thumbnail_small = $return[0]['thumbnail_small'];
    }
}

$tpl->assign('videos', $videos);
if(isset($videos_authors)){
        $tpl->assign('videos_authors', $videos_authors);
}

/* VIMEO RETURN VIDEO DATA

title
    Video title
url
    URL to the Video Page
id
    Video ID
description
    The description of the video
thumbnail_small
    URL to a small version of the thumbnail
thumbnail_medium
    URL to a medium version of the thumbnail
thumbnail_large
    URL to a large version of the thumbnail
user_name
    The user name of the video's uploader
user_url
    The URL to the user profile
upload_date
    The date/time the video was uploaded on
user_portrait_small
    Small user portrait (30px)
user_portrait_medium
    Medium user portrait (100px)
user_portrait_large
    Large user portrait (300px)
stats_number_of_likes
    # of likes
stats_number_of_views
    # of views
stats_number_of_comments
    # of comments
duration
    Duration of the video in seconds
width
    Standard definition width of the video
height
    Standard definition height of the video
tags
    Comma separated list of tags
*/