<?php

/**
 * Start up and setup the app
*/
require_once('../bootstrap.php');

/**
 * Redirect Mobile browsers to mobile site unless a cookie exists.
*/
$app->mobileRouter();

/**
 * Setup view
*/
$tpl = new Template(TEMPLATE_USER);
$tpl->setConfig('opinion');

/**
 * Fetch HTTP variables
*/
$category_name = filter_input(INPUT_GET,'category_name',FILTER_SANITIZE_STRING);
$subcategory_name = filter_input(INPUT_GET,'subcategory_name',FILTER_SANITIZE_STRING);
$authorID = (int) filter_input(INPUT_GET,'author_id',FILTER_SANITIZE_STRING);

/**
 * Redirect to home if category_name is not opinion
*/
if ($category_name !="opinion") { Application::forward('/home/'); }

/**
 * Set up Model
*/
$cm = new ContentManager();
$ccm = new ContentCategoryManager();

/**
 * Fetch information for some uncached parts of the view
*/
require_once ("opinion_index_advertisement.php");

/**
 * Generate the ID for use it to fetch caches
*/
$page = (!isset($_GET['page'])) ? $page = 1 : $page = $_GET['page'];
$cacheID = 'opinion|'.(($authorID != '') ? $authorID.'|' : '').$page;

if (isset($_REQUEST['action'])) {


    switch ($_REQUEST['action']) {
      	case 'list_opinions': // Index frontpage

            // Don't execute the app logic if there are caches available
            if (!$tpl->isCached('opinion/opinion_index.tpl', $cacheID)) {

                // Fetch last opinions from editorial
                $editorial = $cm->find('Opinion',
                                       'opinions.type_opinion=1 '.
                                       'AND contents.available=1 '.
                                       'AND contents.in_home=1 '.
                                       'AND contents.content_status=1 ',
                                       'ORDER BY position ASC, created DESC '.
                                       'LIMIT 2');

                // Fetch last opinions from director
                $director = $cm->find('Opinion',
                                      'opinions.type_opinion=2 '.
                                      'AND contents.available=1 '.
                                      'AND contents.in_home=1 '.
                                      'AND contents.content_status=1 ',
                                      'ORDER BY created DESC LIMIT 2');


				if (isset($director) && !empty($director)) {
	                // Fetch the photo images of the director
	                $aut = new Author($director[0]->fk_author);
					$foto = $aut->get_photo($director[0]->fk_author_img);
					if (isset($foto->path_img)){
						$dir['photo'] = $foto->path_img;
					}
					$dir['name'] = $aut->name;
					$tpl->assign('dir', $dir);
					$tpl->assign('director',  $director[0]);
				}


				//define('ITEMS_PAGE', 2);

                $_limit='LIMIT '.(($page-1)*ITEMS_PAGE).', '.(($page)*ITEMS_PAGE);
                $url='opinion';

				$total_opinions = $cm->count('Opinion','in_home=1 and available=1 and type_opinion=0',
                                      'ORDER BY type_opinion DESC, position ASC, created DESC ');

                // Fetch last opinions of contributors and paginate them by ITEM_PAGE
				$opinions = $cm->find('Opinion', 'in_home=1 and available=1 and type_opinion=0',
                                      'ORDER BY type_opinion DESC, position ASC, created DESC '.$_limit);

				$improvedOpinions = array();
				foreach($opinions as $opinion) {
					$opinion->author = new Author($opinion->fk_author);
					$opinion->name = $opinion->author->name;
					$opinion->author_name_slug = String_Utils::get_title($opinion->name);
					$improvedOpinions[] = $opinion;
				}

                $pagination = $cm->create_paginate($total_opinions, ITEMS_PAGE, 2, 'URL', $url);

				// Fetch information for shared parts
				require_once ('widget_headlines_past.php');
				require_once ("index_sections.php");
				require_once ("widget_static_pages.php");

                $tpl->assign('editorial', $editorial);
                $tpl->assign('opinions',  $improvedOpinions);
                $tpl->assign('pagination',  $pagination);
                $tpl->assign('page', $page);

            }

            $tpl->display('opinion/opinion_frontpage.tpl', $cacheID);

        break;

        case 'list_op_author':  // Author frontpage

            // Don't execute the app logic if there are caches available
            if (!$tpl->isCached('opinion/frontpage_author.tpl', $cacheID)) {

                $_limit=' LIMIT '.(($page-1)*ITEMS_PAGE).', '.(ITEMS_PAGE);

                // Fetch editorial opinions
                if ($authorID==1) { //Editorial

                    $opinions = $cm->find_listAuthorsEditorial('contents.available=1  AND contents.content_status=1', 'ORDER BY created DESC '.$_limit);
                    $total_opinions = $cm->cache->count('Opinion','opinions.type_opinion=1 and contents.available=1  and contents.content_status=1');
                    $name_author= 'editorial';
                    if (!empty($opinions)) {
                        foreach ($opinions as &$opinion) {
                            $opinion['pk_author'] = 1;
                            $opinion['author_name_slug']  = $name_author;
                        }
                    }
                // Fetch director opinions
                } elseif ($authorID == 2) { //Director

                    $opinions = $cm->find_listAuthors('opinions.type_opinion=2 and contents.available=1 and contents.content_status=1', 'ORDER BY created DESC '.$_limit);
                    $total_opinions = $cm->cache->count('Opinion','opinions.type_opinion=2 and contents.available=1  and contents.content_status=1');
                    $name_author = 'director';
                    if (!empty($opinions)) {
                        foreach ($opinions as &$opinion) {
                            $opinion['pk_author'] = 2;
                            $opinion['author_name_slug']  = $name_author;
                        }
                    }
                // Fetch common author opinions
                } else { //Author

                    // First, I need to know the amount of opinions for if it is necessary to paginate.
                    $total_opinions = $cm->count('Opinion',
												 'opinions.type_opinion=0 and opinions.fk_author='.($authorID)
												 .' AND contents.available=1 AND contents.content_status=1');
                    $opinions = $cm->find_listAuthors('opinions.type_opinion=0 and opinions.fk_author='.($authorID)
													  .' and contents.available=1  and contents.content_status=1',
													  'ORDER BY created DESC '.$_limit);
                    $aut = new Author($authorID);
                    $name_author = String_Utils::get_title($aut->name);
                    if (!empty($opinions)) {
                        foreach ($opinions as &$opinion) {
                            $opinion['author_name_slug']  = String_Utils::get_title($opinion['name']);
                        }
                    }

                }



                // If there aren't opinions just redirect to homepage opinion
                if(empty($total_opinions)){ Application::forward301('/seccion/opinion/'); }

                $url = Uri::generate('opinion_author_frontpage',
							  array(
									'slug' => $opinions[0]['author_name_slug'],
									'id' => $opinions[0]['pk_author']
									));

                $pagination = $cm->create_paginate($total_opinions, ITEMS_PAGE, 2, 'URL', $url);

				// Fetch information for shared parts
				require_once ('widget_headlines_past.php');
				require_once ("index_sections.php");
				require_once ("widget_static_pages.php");

                $tpl->assign('author_name', $name_author);
                $tpl->assign('pagination_list', $pagination);
                $tpl->assign('opinions', $opinions);
                $tpl->assign('author_id', $authorID);
                $tpl->assign('page', $page);

            } // End if isCached

            $tpl->display('opinion/opinion_author_index.tpl', $cacheID);
        break;
    }
}
