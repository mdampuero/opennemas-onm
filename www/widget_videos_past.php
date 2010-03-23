<?php
$videos_viewed = $cm->cache->getMostViewedContent('Video', true, $category_data['id']);
$videos_comments = $cm->cache->getMostComentedContent('Video', true, $category_data['id']);
foreach($videos_viewed as $video){
    if($video->author_name =='vimeo'){
        $url="  http://vimeo.com/api/v2/video/'.$video->videoid.'.php";
        $curl = curl_init( 'http://vimeo.com/api/v2/video/'.$video->videoid.'.php');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_TIMEOUT, 50);
        $return = curl_exec($curl);
        $return = unserialize($return);
        curl_close($curl);
        $video->thumbnail_medium = $return[0]['thumbnail_medium'];
        $video->thumbnail_small = $return[0]['thumbnail_small'];
    }
    $video->category_name = $video->loadCategoryName($video->id);
    $video->category_title = $video->loadCategoryTitle($video->id);
}
foreach($videos_comments as $video){
    if($video->author_name =='vimeo'){
        $url="  http://vimeo.com/api/v2/video/'.$video->videoid.'.php";
        $curl = curl_init( 'http://vimeo.com/api/v2/video/'.$video->videoid.'.php');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_TIMEOUT, 50);
        $return = curl_exec($curl);
        $return = unserialize($return);
        curl_close($curl);
        $video->thumbnail_medium = $return[0]['thumbnail_medium'];
        $video->thumbnail_small = $return[0]['thumbnail_small'];
    }
    $video->category_name = $video->loadCategoryName($video->id);
    $video->category_title = $video->loadCategoryTitle($video->id);
}
$tpl->assign('videos_viewed', $videos_viewed);
$tpl->assign('videos_comments', $videos_comments);


