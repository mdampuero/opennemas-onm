<?php
 $category_id = $category;
if(isset($thisvideo->category) && !empty($thisvideo->category)){
     $category_id = $thisvideo->category;
}
$videos_viewed = $cm->cache->getMostViewedContent('Video', true, $category_id);
$videos_voted = $cm->getMostVotedContent('Video', true, $category_id);
$videos_comments = $cm->cache->getMostComentedContent('Video', true, $category_id);

if(!empty($videos_viewed) && count($videos_viewed)>0){

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
        $video->category_name = $video->loadCategoryName($video->pk_content);
        $video->category_title = $video->loadCategoryTitle($video->pk_content);
    }
}

$videos_commented = array();
if(!empty($videos_comments) && count($videos_comments)>0){    
    foreach($videos_comments as $ar_video){
        $video = new Video($ar_video['pk_content']);
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
         $video->category_name = $video->loadCategoryName($video->pk_content);
         $video->category_title = $video->loadCategoryTitle($video->pk_content);
         $videos_commented[]=$video;
    }
}

if(!empty($videos_voted) && count($videos_voted)>0){

    foreach($videos_voted as $video){

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
         $video->category_name = $video->loadCategoryName($video->pk_content);
         $video->category_title = $video->loadCategoryTitle($video->pk_content);

    }
    
}

$tpl->assign('videos_viewed', $videos_viewed);
$tpl->assign('videos_comments', $videos_commented);
$tpl->assign('videos_voted', $videos_voted);


 