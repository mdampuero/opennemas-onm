<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Frontend\Controllers;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Onm\Framework\Controller\Controller;
use Onm\Message as m;
use Onm\Settings as s;

/**
 * Handles the actions for advertisements
 *
 * @package Backend_Controllers
 **/
class OpinionsController extends Controller
{
    /**
     * Common code for all the actions
     *
     * @return void
     **/
    public function init()
    {
        $this->view = new \Template(TEMPLATE_USER);
        $this->view->setConfig('opinion');

        $this->page = $this->request->query->getDigits('page', 1);

        $this->category_name = $this->request->query->filter('category_name', 'opinion', FILTER_SANITIZE_STRING);
        $this->view->assign('actual_category', 'opinion'); // Used in renderMenu

        $this->cm = new \ContentManager();
    }

    /**
     * Renders the opinion frontpage
     *
     * @return Response the response object
     **/
    public function frontpageAction(Request $request)
    {
        //*** LITTLE HACK TO TEST THIS ACTION BECAUSE IN opinion/opinion_frontpage.tpl
        //*** IS ACCESS TO $smarty.get AND $smarty.request
        //*** Change this (It is inclide in TODO on MIGRATION_FRONTEND_SYMFONY)
        $_REQUEST["action"] = "list_opinions";
        $_GET["page"] = $this->page;
        //*** <--------------------------------------------

        // Index frontpage
        $cacheID = $this->view->generateCacheId($this->category_name, '', $this->page);

        // Don't execute the app logic if there are caches available
        if (($this->view->caching == 0)
            || !$this->view->isCached('opinion/opinion_index.tpl', $cacheID)) {

            // Fetch last opinions from editorial
            $editorial = $this->cm->find(
                'Opinion',
                'opinions.type_opinion=1 '.
                'AND contents.available=1 '.
                'AND contents.in_home=1 '.
                'AND contents.content_status=1 ',
                'ORDER BY position ASC, created DESC '.
                'LIMIT 2'
            );

            // Fetch last opinions from director
            $director = $this->cm->find(
                'Opinion',
                'opinions.type_opinion=2 '.
                'AND contents.available=1 '.
                'AND contents.in_home=1 '.
                'AND contents.content_status=1 ',
                'ORDER BY created DESC LIMIT 2'
            );

            if (isset($director) && !empty($director)) {
                // Fetch the photo images of the director
                $aut = new \Author($director[0]->fk_author);
                $foto = $aut->get_photo($director[0]->fk_author_img);
                if (isset($foto->path_img)) {
                    $dir['photo'] = $foto->path_img;
                }
                $dir['name'] = $aut->name;
                $this->view->assign('dir', $dir);
                $this->view->assign('director', $director[0]);
            }

            if ($this->page == 1) {
                $opinions = $this->cm->find(
                    'Opinion',
                    'in_home=1 and available=1 and type_opinion=0',
                    'ORDER BY position ASC, starttime DESC '
                );
                $totalHome = count($opinions);

            } else {
                $_limit ='LIMIT '.(($this->page-2)*ITEMS_PAGE).', '.(($this->page-1)*ITEMS_PAGE);
                // Fetch last opinions of contributors and
                // paginate them by ITEM_PAGE
                $opinions = $this->cm->find(
                    'Opinion',
                    'in_home=0 and available=1 and type_opinion=0',
                    'ORDER BY starttime DESC '.$_limit
                );
            }
            // Added ITEMS_PAGE for count first page
            $total_opinions =  ITEMS_PAGE + $this->cm->count(
                'Opinion',
                'in_home=0 and available=1 and type_opinion=0',
                'ORDER BY type_opinion DESC, created DESC '
            );

            $authors = array();
            foreach ($opinions as &$opinion) {
                if (!array_key_exists($opinion->fk_author, $authors)) {
                    $author = new \Author($opinion->fk_author);
                    $author->get_author_photos();
                    $authors[$opinion->fk_author] = $author;
                }
                $opinion->author           = $authors[$opinion->fk_author];
                $opinion->name             = $opinion->author->name;
                $opinion->author_name_slug =   \StringUtils::get_title($opinion->name);
                $opinion->author->uri = \Uri::generate(
                    'opinion_author_frontpage',
                    array(
                        'slug' => $opinion->author->name,
                        'id' => $opinion->author->pk_author
                    )
                );
            }

            $url ='opinion';
            $pagination = $this->cm->create_paginate(
                $total_opinions,
                ITEMS_PAGE,
                2,
                'URL',
                $url,
                ''
            );

            $this->view->assign('editorial', $editorial);
            $this->view->assign('opinions', $opinions);
            $this->view->assign('authors', $authors);
            $this->view->assign('pagination', $pagination);
            $this->view->assign('page', $this->page);
        }

        $this->advertisements();

        return $this->render(
            'opinion/opinion_frontpage.tpl',
            array('cache_id' => $cacheID)
        );
    }

    /**
     * Renders the opinion author's frontpage
     *
     * @return Response the response object
     **/
    public function frontpageAuthorAction(Request $request)
    {
        // Index author's frontpage
        $authorID   = $request->query->getDigits('author_id', null);
        if (empty($authorID)) {
            return new RedirectResponse($this->generateUrl('frontend_opinion_frontpage'));
        }

        // Author frontpage
        $cacheID = $this->view->generateCacheId($this->category_name, $authorID, $this->page);
        // Don't execute the app logic if there are caches available
        if (($this->view->caching == 0)
            || !$this->view->isCached('opinion/frontpage_author.tpl', $cacheID)) {

            // Get author info
            $author = new \Author($authorID);
            $author->get_author_photos();
            $photos = $author->get_author_photos();

            $authorSlug = $request->query->filter('author_slug', null, FILTER_SANITIZE_STRING);

            // Setting filters for the further SQLs
            if ($authorID == 1 && strtolower($authorSlug) == 'editorial') {
                // Editorial
                $filter = 'opinions.type_opinion=1';
                $authorName = 'editorial';
            } elseif ($authorID == 2 && strtolower($authorSlug) == 'director') {
                // Director
                $filter =  'opinions.type_opinion=2';
                $authorName = 'director';
            } else {
                // Regular authors
                $filter = 'opinions.type_opinion=0 AND opinions.fk_author='.$authorID;
                $authorName = \StringUtils::get_title($author->name);
            }

            $_limit=' LIMIT '.(($this->page-1)*ITEMS_PAGE).', '.(ITEMS_PAGE);

            // Get the number of total opinions for this
            // author for pagination purpouses
            $countOpinions = $this->cm->cache->count(
                'Opinion',
                $filter
                .' AND contents.available=1  and contents.content_status=1 '
            );

            // Get the list articles for this author
            $opinions = $this->cm->getOpinionArticlesWithAuthorInfo(
                $filter
                .' AND contents.available=1 and contents.content_status=1',
                'ORDER BY created DESC '.$_limit
            );

            if (!empty($opinions)) {
                foreach ($opinions as &$opinion) {
                    $opinion['pk_author'] = $authorID;
                    $opinion['author_name_slug']  = $authorName;

                    $opinion['uri'] = $this->generateUrl(
                        'frontend_opinion_show_with_author_slug',
                        array(
                            'opinion_id'    => date('YmdHis', strtotime($opinion['created'])).$opinion['id'],
                            'author_name'   => $opinion['author_name_slug'],
                            'opinion_title' => $opinion['slug'],
                        )
                    );

                    $opinion['author_uri'] = $this->generateUrl(
                        'frontend_opinion_author_frontpage',
                        array(
                            'author_id' => $opinion['pk_author'],
                            'author_slug' => $opinion['author_name_slug'],
                        )
                    );
                }
            }

            $url = $this->generateUrl(
                'frontend_opinion_author_frontpage',
                array(
                    'author_id' => $author->pk_author,
                    'author_slug' => $authorName,
                )
            );

            $pagination = $this->cm->create_paginate(
                $countOpinions,
                ITEMS_PAGE,
                2,
                'URL',
                $url,
                ''
            );

            // Clean weird variables from this assign (must check
            // all the templates)
            // pagination_list cahnge to pagination
            // drop author_id, $author_name as they are inside author var
            $this->view->assign(
                array(
                    'pagination_list' => $pagination,
                    'opinions'        => $opinions,
                    'author_id'       => $authorID,
                    'author_slug'     => strtolower($authorSlug),
                    'author'          => $author,
                    'author_name'     => $author->name,
                    'page'            => $this->page,
                )
            );

        } // End if isCached

        $this->advertisements();

        return $this->render(
            'opinion/opinion_author_index.tpl',
            array('cache_id' => $cacheID)
        );

    }

    /**
     * Displays an opinion given its id
     *
     * @param int opinion_id the identificator of the opinion to show
     *
     * @return Response the response object
     **/
    public function showAction(Request $request)
    {
        $dirtyID = $request->query->getDigits('opinion_id');
        $opinionID = \Content::resolveID($dirtyID);

        // Redirect to opinion frontpage if opinion_id wasn't provided
        if (empty($opinionID)) {
            return new RedirectResponse($this->generateUrl('frontend_opinion_frontpage'));
        }

        $opinion = new \Opinion($opinionID);

        // TODO: Think that this comments related code can be deleted.
        if (($opinion->available != 1) || ($opinion->in_litter != 0)) {
            throw new \Symfony\Component\Routing\Exception\ResourceNotFoundException();
        }

        $this->getAds('inner');

        // Don't execute the app logic if there are caches available
        $cacheID = $this->view->generateCacheId($this->category_name, '', $opinionID);
        if (($this->view->caching == 0)
            || !$this->view->isCached('opinion/opinion.tpl', $cacheID)) {

            $this->view->assign('contentId', $opinionID);

            $author = new \Author($opinion->fk_author);
            $author->get_author_photos();
            $opinion->author = $author;

            // Please SACAR esta broza de aqui {
            $title = \StringUtils::get_title($opinion->title);
            $print_url = '/imprimir/' . $title. '/'. $opinion->pk_content . '.html';
            $this->view->assign('print_url', $print_url);
            $this->view->assign(
                'sendform_url',
                '/controllers/opinion_inner.php?action=sendform&opinion_id=' . $dirtyID
            );
            // } Sacar broza

            //Rescato esta asignación para que genere correctamente el enlace a frontpage de opinion
            $opinion->author_name_slug = \StringUtils::get_title($opinion->name);
            /*
            //Check slug
            if (empty($slug) || ($opinion->slug != $slug)
                || ($opinion->author_name_slug != $author_name)) {
                Application::forward301(SITE_URL.$opinion->uri);
            }
            */

            // Fetch rating for this opinion
            $rating = new \Rating($opinionID);
            $this->view->assign('rating_bar', $rating->render('article', 'vote'));

            // Fetch suggested contents
            $objSearch = \cSearch::getInstance();
            $suggestedContents = $objSearch->searchSuggestedContents(
                $opinion->metadata,
                'Opinion',
                " contents.available=1 AND pk_content = pk_fk_content",
                4
            );

            // Get author slug for suggested opinions
            foreach ($suggestedContents as &$suggest) {
                $element = new \Opinion($suggest['pk_content']);
                if (!empty($element->author)) {
                    $suggest['author_name'] = $element->author;
                    $suggest['author_name_slug'] = \StringUtils::get_title($element->author);
                } else {
                    $suggest['author_name_slug'] = "author";
                }
                $suggest['uri'] = $this->generateUrl(
                    'frontend_opinion_show_with_author_slug',
                    array(
                        'opinion_id'    => date('YmdHis', strtotime($suggest['created'])).$suggest['pk_content'],
                        'author_name'   => $suggest['author_name_slug'],
                        'opinion_title' => \StringUtils::get_title($suggest['title']),
                    )
                );
            }

            $suggestedContents= $this->cm->getInTime($suggestedContents);
            $this->view->assign('suggested', $suggestedContents);

            // Fetch the other opinions for this author
            if ($opinion->type_opinion == 1) {
                $where =' opinions.type_opinion = 1';
                $opinion->name = 'Editorial';
            } elseif ($opinion->type_opinion == 2) {
                $where =' opinions.type_opinion = 2';
                $opinion->name = 'Director';
            } else {
                $where =' opinions.fk_author='.($opinion->fk_author);
            }

            $otherOpinions = $this->cm->cache->find(
                'Opinion',
                $where.' AND `pk_opinion` <>' .$opinionID
                .' AND available = 1  AND content_status=1',
                ' ORDER BY created DESC LIMIT 0,9'
            );

            $author = new \Author($opinion->fk_author);
            $author->get_author_photos();

            foreach ($otherOpinions as &$otOpinion) {
                $otOpinion->author = $author;
                $otOpinion->author_name_slug  = $opinion->author_name_slug;
                $otOpinion->uri  = $otOpinion->uri;
            }

            $this->view->assign(
                array(
                    'other_opinions'  => $otherOpinions,
                    'opinion'         => $opinion,
                    'author'          => $author,
                )
            );

        } // End if isCached

        // Show in Frontpage
        return $this->render(
            'opinion/opinion.tpl',
            array('cache_id' => $cacheID)
        );
    }

    /**
     * Fetches the advertisement
     *
     **/
    private function getAds($context = 'frontpage')
    {
        if ($context == 'inner') {
            $positions = array(701, 702, 703, 704, 705, 706, 707, 708, 709, 710);
            $intersticialId = 750;
        } else {
            $positions = array(601, 602, 603, 605, 609, 610);
            $intersticialId = 650;
        }

        $ccm = \ContentCategoryManager::get_instance();
        $category = $ccm->get_id($this->category_name);
        $category = (!isset($category) || ($category=='home'))? 0: $category;

        $advertisement = \Advertisement::getInstance();

        $banners = $advertisement->getAdvertisements($positions, $category);
        $banners = $this->cm->getInTime($banners);

        $advertisement->renderMultiple($banners, $advertisement);

        // Get intersticial banner
        $intersticial = $advertisement->getIntersticial($intersticialId, $category);
        if (!empty($intersticial)) {
            $advertisement->renderMultiple(array($intersticial), $advertisement);
        }
    }
}
