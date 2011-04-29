<?php

/**
 * Setup app
*/
require_once('../bootstrap.php');
require_once('./session_bootstrap.php');

/**
 * Setup view
*/
$tpl = new TemplateAdmin(TEMPLATE_ADMIN);

echo "<br>";
$cm = new ContentManager();

if(isset($_REQUEST['action']) ) {
	switch($_REQUEST['action']) {
		case 'list':
            //$videos = $cm->find('video', 'fk_content_type=9 ', 'ORDER BY created DESC');
            list($videos, $pager)= $cm->find_pages('Video', 'fk_content_type=9 ', 'ORDER BY  created DESC ',$_REQUEST['page'],20);
            break;

        case 'list_by_metadatas':
            $presentSearch = cSearch::Instance();
            $arrayIds = $presentSearch->SearchContentsSelect('pk_content', $_REQUEST['metadatas'], 'video',  100);

            if(!empty($arrayIds)) {
                $szWhere = '( false ';
                foreach($arrayIds as $Id)                 {
                    $szWhere .= ' OR pk_content = ' . $Id[0];
                }
                $szWhere .= ')';
            } else {
                $szWhere = "TRUE";
            }
            //$videos = $cm->find('video', 'fk_content_type = 9 AND ' . $szWhere, 'ORDER BY created DESC');
  			list($videos, $pager)= $cm->find_pages('Video', 'fk_content_type=9 AND ' . $szWhere, 'ORDER BY  created DESC ',$_REQUEST['page'],20);

            break;
    }
}
/*
$videos=$cm->paginate_num($videos,20);
$pages=$cm->pager;
if($pages->_totalPages>1){
    echo "<p align='center'>Paginas: ";
    for($i=1;$i<=$pages->_totalPages;$i++){
            echo ' <a style="cursor:pointer;" onClick="get_search_videos(0,'.$i.')">'.$i.'</a> ';
    }
    echo "</p> ";
}
*/
$tpl->assign('videos', $videos);


echo "<ul id='thelist' class='clearfix gallery_list' style='width: 100%; margin: 0pt; padding: 0pt;'> ";
if($videos) {
	$num = 0;
    foreach ($videos as $as){
        $ph=new video($as->pk_video);
        echo '<li><div style="float: left;"> <a>
              <img class="video" width="67" src="http://i4.ytimg.com/vi/'. $ph->videoid .'/default.jpg" id="draggable_video'.$num.'" name="'.$as->pk_video.'" alt="'.$as->title .'" qlicon="' . $ph->url . '" title="' . $ph->title . '" de:description="' .htmlspecialchars(stripslashes($ph->description), ENT_QUOTES). '" de:tags="' . $ph->metadata . '" de:created="' . $ph->created . '"  title="Desc:'.htmlspecialchars(stripslashes($ph->description), ENT_QUOTES) .' Tags:'.$ph->metadata.'"  />
              </a> </div></li>';
        $num++;

    }
}
echo "	 </ul><br>";

if(isset($pager) && !empty($pager)) {
    $paginacion = $cm->makePagesLinkjs($pager, 'get_search_videos', 0);
    if($pager->_totalPages>1) {
        echo "<div align=\"center\" class=\"pagination\"> " . $paginacion . "</div>";
    }
}