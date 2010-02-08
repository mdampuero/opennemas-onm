<?php


/*********************************************************************************/
 // $alltypes=array(1=>'foto',2=>'video',3=>'carta',4=>'opinion',6=>'enquisa');

 
            $pc_cc = new PC_ContentCategoryManager();
            $pc_cm = new PC_ContentManager();

            //'pc_photo':
            $allcategorys = $pc_cc->find_by_type('1', 'inmenu=1 and available=1', 'ORDER BY posmenu');
            $photo_categorys= array();
            foreach($allcategorys as $category){             
                $id=$category->pk_content_category;
                $photo_categorys[$id]->title= $category->title;
                $photo_categorys[$id]->name= $category->name;
                $photo_categorys[$id]->contents = $pc_cm->find_by_category('pc_photo', $id, 'content_status=0 AND available=1', 'ORDER BY favorite DESC, created DESC   LIMIT 0, 1');
            }
            $tpl->assign('photo_categorys', $photo_categorys);

            // 'pc_video':
            $allcategorys = $pc_cc->find_by_type('2', 'inmenu=1  and available=1', 'ORDER BY posmenu');
            $video_categorys= array();
            foreach($allcategorys as $category){
                $id=$category->pk_content_category;
                $video_categorys[$id]->title= $category->title;
                $video_categorys[$id]->name= $category->name;
                $video_categorys[$id]->contents = $pc_cm->find_by_category('pc_video', $id, 'content_status=0 AND available=1', 'ORDER BY favorite DESC, created DESC   LIMIT 0, 1');
            }
            $tpl->assign('video_categorys', $video_categorys);

            //'pc_opinion':
            $allcategorys = $pc_cc->find_by_type('4', 'inmenu=1 and available=1', 'ORDER BY posmenu');
            $opinion_categorys= array();
            foreach($allcategorys as $category){
                $id=$category->pk_content_category;
                $opinion_categorys[$id]->title= $category->title;
                $opinion_categorys[$id]->name= $category->name;
                $opinion_categorys[$id]->contents = $pc_cm->find_by_category('pc_opinion', $id, 'content_status=0 AND available=1', 'ORDER BY favorite DESC, created DESC  LIMIT 0, 2');
            }
            $tpl->assign('opinion_categorys', $opinion_categorys);

            //'pc_letter':
            $allcategorys = $pc_cc->find_by_type('3', 'inmenu=1 and available=1', 'ORDER BY posmenu');
            $letter_categorys= array();
            foreach($allcategorys as $category){
                $id=$category->pk_content_category;
                $letter_categorys[$id]->title= $category->title;
                $letter_categorys[$id]->name= $category->name;
                $letter_categorys[$id]->contents = $pc_cm->find_by_category('pc_letter', $id, 'content_status=0 AND available=1', 'ORDER BY favorite DESC, created DESC  LIMIT 0, 2');
            }
            $tpl->assign('letter_categorys', $letter_categorys);

            //'pc_poll': //category 7 enquisa
            $polls = $cm->find('PC_Poll', 'content_status=1 and available=1', 'ORDER BY changed DESC LIMIT 0,2');
            $tpl->assign('polls', $polls);

            $users = PC_User::get_instance();
            $conecta_users = $users->get_all_authors();
            $tpl->assign('conecta_users', $conecta_users);
 
