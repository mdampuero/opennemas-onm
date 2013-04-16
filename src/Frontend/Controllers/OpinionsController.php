<?php
/**
 * Handles the actions for advertisements
 *
 * @package Frontend_Controllers
 **/
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
 * @package Frontend_Controllers
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
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function frontpageAction(Request $request)
    {
        if ($this->page == 1) {
            $where = '';
            $orderBy='ORDER BY contents.in_home DESC, position ASC, created DESC ';
        } else {
            $where = 'AND contents.in_home=0 ';
            $orderBy='ORDER BY created DESC ';
        }
        // Index frontpage
        $cacheID = $this->view->generateCacheId($this->category_name, '', $this->page);

        // Don't execute the app logic if there are caches available
        if (($this->view->caching == 0)
            || !$this->view->isCached('opinion/opinion_index.tpl', $cacheID)
        ) {

            // Fetch last opinions from editorial
            $configurations = s::get('opinion_settings');

            $totalEditorial = 2;
            $totalDirector = 1;
            if (!empty($configurations) && array_key_exists('total_editorial', $configurations)) {
                $totalEditorial = $configurations['total_editorial'];
            }
            if (!empty($configurations) && array_key_exists('total_director', $configurations)) {
                $totalDirector = $configurations['total_director'];
            }

            $editorial = array();
            if (!empty($totalEditorial)) {
                $editorial = $this->cm->find(
                    'Opinion',
                    'opinions.type_opinion=1 '.
                    'AND contents.available=1 '.
                    $where.
                    'AND contents.content_status=1 ',
                    $orderBy.
                    'LIMIT '.$totalEditorial
                );
            }

            foreach ($editorial as &$opinion) {
                $item = new \Content();
                $item->loadAllContentProperties($opinion->pk_content);

                $opinion->summary = $item->summary;
                $opinion->img1_footer = $item->img1_footer;
                if (isset($item->img1) && ($item->img1 > 0)) {
                    $opinion->img1 = new \Photo($item->img1);
                }
            }

            // Fetch last opinions from director
            $director = array();
            if (!empty($totalDirector)) {
                $director = $this->cm->find(
                    'Opinion',
                    'opinions.type_opinion=2 '.
                    'AND contents.available=1 '.
                    $where.
                    'AND contents.content_status=1 ',
                    $orderBy.
                    ' LIMIT '.$totalDirector
                );
            }

            if (isset($director) && !empty($director)) {
                // Fetch the photo images of the director
                $aut = new \Author($director[0]->fk_author);
                $foto = $aut->get_photo($director[0]->fk_author_img);
                if (isset($foto->path_img)) {
                    $dir['photo'] = $foto->path_img;
                }
                $item = new \Content();
                $item->loadAllContentProperties($director[0]->pk_content);
                $dir['summary'] = $item->summary;
                $dir['img1_footer'] = $item->img1_footer;
                if (isset($item->img1) && ($item->img1 > 0)) {
                    $dir['img1'] = new \Photo($item->img1);
                }
                $dir['name'] = $aut->name;
                $item = new \Content();
                $item->loadAllContentProperties($director[0]->pk_content);
                $director[0]->summary = $item->summary;
                $director[0]->img1_footer = $item->img1_footer;
                if (isset($item->img1) && ($item->img1 > 0)) {
                    $director[0]->img1 = new \Photo($item->img1);
                }

                $this->view->assign(
                    array(
                        'dir'      => $dir,
                        'director' => $director[0],
                        'opinionsDirector' => $director
                    )
                );
            }

            $itemsPerPage = s::get('items_per_page');

            list($countOpinions, $opinions)= $this->cm->getCountAndSlice(
                'Opinion',
                null,
                'opinions.type_opinion=0 '.
                $where.
                'AND contents.available=1 '.
                'AND contents.content_status=1 ',
                $orderBy,
                $this->page,
                $itemsPerPage
            );
            $pagination = \Pager::factory(
                array(
                    'mode'        => 'Sliding',
                    'perPage'     => $itemsPerPage,
                    'append'      => false,
                    'path'        => '',
                    'delta'       => 3,
                    'clearIfVoid' => true,
                    'urlVar'      => 'page',
                    'totalItems'  => $countOpinions,
                    'fileName'    => $this->generateUrl(
                        'frontend_opinion_frontpage'
                    ).'/?page=%d',
                )
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
                $item = new \Content();
                $item->loadAllContentProperties($opinion->pk_content);
                $opinion->summary = $item->summary;
                $opinion->img1_footer = $item->img1_footer;
                if (isset($item->img1) && ($item->img1 > 0)) {
                    $opinion->img1 = new \Photo($item->img1);
                }

                $opinion->author->uri = \Uri::generate(
                    'opinion_author_frontpage',
                    array(
                        'slug' => $opinion->author->name,
                        'id' => $opinion->author->pk_author
                    )
                );
            }

            $this->view->assign(
                array(
                    'editorial'  => $editorial,
                    'opinions'   => $opinions,
                    'authors'    => $authors,
                    'pagination' => $pagination,
                    'page'       => $this->page
                )
            );
        }

        $this->getAds();

        return $this->render(
            'opinion/opinion_frontpage.tpl',
            array('cache_id' => $cacheID)
        );
    }

    /**
     * Renders the opinion frontpage
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function extFrontpageAction(Request $request)
    {
        if ($this->page == 1) {
            $where = '';
            $orderBy='ORDER BY contents.in_home DESC, position ASC, created DESC ';
        } else {
            $where = 'AND contents.in_home=0 ';
            $orderBy='ORDER BY created DESC ';
        }
        // Index frontpage
        $cacheID = $this->view->generateCacheId($this->category_name, '', $this->page);

        // Don't execute the app logic if there are caches available
        if (($this->view->caching == 0)
            || !$this->view->isCached('opinion/opinion_index.tpl', $cacheID)
        ) {

            // Getting Synchronize setting params
            $wsUrl = '';
            $syncParams = s::get('sync_params');
            foreach ($syncParams as $siteUrl => $categoriesToSync) {
                foreach ($categoriesToSync as $value) {
                    if (preg_match('/'.$this->category_name.'/i', $value)) {
                        $wsUrl = $siteUrl;
                    }
                }
            }

            // Fetch last external opinions from editorial
            $editorial = $this->cm->getUrlContent($wsUrl.'/ws/opinions/editorialinhome/', true);

            // Fetch last external opinions from director
            $director = $this->cm->getUrlContent($wsUrl.'/ws/opinions/directorinhome/', true);

            // Some director logic
            if (isset($director) && !empty($director)) {
                // Fetch the photo images of the director
                $aut = $this->cm->getUrlContent($wsUrl.'/ws/authors/id/'.$director[0]->fk_author, true);
                $foto = $this->cm->getUrlContent(
                    $wsUrl.'/ws/authors/photo/'.$director[0]->fk_author,
                    true
                );

                if (isset($foto->path_img)) {
                    $dir['photo'] = $foto->path_img;
                }

                $dir['name'] = $aut->name;
                $this->view->assign('dir', $dir);
                $this->view->assign('director', $director[0]);
            }

            if ($this->page == 1) {
                $opinions = $this->cm->getUrlContent($wsUrl.'/ws/opinions/authorsinhome/', true);
                $totalHome = count($opinions);
            } else {
                // Fetch last opinions of contributors and paginate them by ITEM_PAGE
                $opinions = $this->cm->getUrlContent($wsUrl.'/ws/opinions/authorsnotinhomepaged/'.$this->page, true);
            }

            // Sum of total opinions in home + not in home for the pager
            $totalOpinions =  ITEMS_PAGE + (int)$this->cm->getUrlContent(
                $wsUrl.'/ws/opinions/countauthorsnotinhome/',
                true
            );

            $authors = array();
            foreach ($opinions as &$opinion) {
                if (!array_key_exists($opinion->fk_author, $authors)) {
                    $author = $this->cm->getUrlContent($wsUrl.'/ws/authors/id/'.$opinion->fk_author, true);
                    $authors[$opinion->fk_author] = $author;
                }
                $opinion->author           = $authors[$opinion->fk_author];
                $opinion->name             = $opinion->author->name;
                $opinion->author_name_slug = \StringUtils::get_title($opinion->name);
                $opinion->author->uri = $this->generateUrl(
                    'frontend_opinion_external_author_frontpage',
                    array(
                        'author_id' => $opinion->fk_author,
                        'author_slug' => $opinion->author_name_slug,
                    )
                );
            }

            $itemsPerPage = s::get('items_per_page');
            // Get external media url for author images
            $externalMediaUrl = $this->cm->getUrlContent($wsUrl.'/ws/instances/mediaurl/', true);

            $pagination = \Pager::factory(
                array(
                    'mode'        => 'Sliding',
                    'perPage'     => $itemsPerPage,
                    'append'      => false,
                    'path'        => '',
                    'delta'       => 3,
                    'clearIfVoid' => true,
                    'urlVar'      => 'page',
                    'totalItems'  => $totalOpinions,
                    'fileName'    => $this->generateUrl(
                        'frontend_opinion_external_frontpage'
                    ).'/?page=%d',
                )
            );

            $this->view->assign(
                array(
                    'editorial'  => $editorial,
                    'opinions'   => $opinions,
                    'authors'    => $authors,
                    'pagination' => $pagination,
                    'page'       => $this->page,
                    'ext'        => $externalMediaUrl,
                )
            );
        }

        $this->getAds();

        return $this->render(
            'opinion/opinion_frontpage.tpl',
            array('cache_id' => $cacheID)
        );
    }


    /**
     * Renders the opinion author's frontpage
     *
     * @param Request $request the request object
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
            || !$this->view->isCached('opinion/frontpage_author.tpl', $cacheID)
        ) {

            // Get author info
            $author = new \Author($authorID);
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

            $itemsPerPage = s::get('items_per_page');

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
                    $item = new \Content();
                    $item->loadAllContentProperties($opinion['pk_content']);
                    $opinion['summary'] = $item->summary;
                    $opinion['img1_footer'] = $item->img1_footer;
                    $opinion['pk_author'] = $authorID;
                    $opinion['author_name_slug']  = $authorName;
                    if (isset($item->img1) && ($item->img1 > 0)) {
                        $opinion['img1'] = new \Photo($item->img1);
                    }

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

            $pagination = \Pager::factory(
                array(
                    'mode'        => 'Sliding',
                    'perPage'     => $itemsPerPage,
                    'append'      => false,
                    'path'        => '',
                    'delta'       => 4,
                    'clearIfVoid' => true,
                    'urlVar'      => 'page',
                    'totalItems'  => $countOpinions,
                    'fileName'    => $this->generateUrl(
                        'frontend_opinion_author_frontpage',
                        array(
                            'author_id' => $author->pk_author,
                            'author_slug' => $authorName,
                        )
                    ).'/?page=%d',
                )
            );

            // Clean weird variables from this assign (must check
            // all the templates)
            // pagination_list change to pagination
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

        $this->getAds();

        return $this->render(
            'opinion/opinion_author_index.tpl',
            array('cache_id' => $cacheID)
        );

    }


    /**
     * Renders the external opinion author's frontpage
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function extFrontpageAuthorAction(Request $request)
    {
        // Fetch HTTP params
        $authorID   = $request->query->getDigits('author_id', null);
        $authorSlug = $request->query->filter('author_slug', null, FILTER_SANITIZE_STRING);

        if (empty($authorID)) {
            return new RedirectResponse($this->generateUrl('frontend_opinion_frontpage'));
        }

        // Author frontpage
        $cacheID = $this->view->generateCacheId($this->category_name, $authorID, $this->page);
        // Don't execute the app logic if there are caches available
        if (($this->view->caching == 0)
            || !$this->view->isCached('opinion/frontpage_author.tpl', $cacheID)) {

            // Getting Synchronize setting params
            $wsUrl = '';
            $syncParams = s::get('sync_params');
            foreach ($syncParams as $siteUrl => $categoriesToSync) {
                foreach ($categoriesToSync as $value) {
                    if (preg_match('/'.$this->category_name.'/i', $value)) {
                        $wsUrl = $siteUrl;
                    }
                }
            }

            // Get author info
            $author = $this->cm->getUrlContent($wsUrl.'/ws/authors/id/'.$authorID, true);

            // Setting filters for the further SQLs
            if ($authorID == 1 && strtolower($authorSlug) == 'editorial') {
                // Editorial
                $authorName = 'editorial';
                $countOpinions = $this->cm->getUrlContent(
                    $wsUrl.'/ws/opinions/counteditorialopinions/',
                    true
                );
                $opinions = $this->cm->getUrlContent(
                    $wsUrl.'/ws/opinions/allopinionseditorial/'.$this->page,
                    true
                );
            } elseif ($authorID == 2 && strtolower($authorSlug) == 'director') {
                // Director
                $authorName = 'director';
                $countOpinions = $this->cm->getUrlContent(
                    $wsUrl.'/ws/opinions/countdirectoropinions/',
                    true
                );
                $opinions = $this->cm->getUrlContent(
                    $wsUrl.'/ws/opinions/allopinionsdirector/'.$this->page,
                    true
                );
            } else {
                // Regular authors
                $authorName = \StringUtils::get_title($author->name);
                $countOpinions = $this->cm->getUrlContent(
                    $wsUrl.'/ws/opinions/countauthoropinions/'.$authorID,
                    true
                );
                $opinions = $this->cm->getUrlContent(
                    $wsUrl.'/ws/opinions/allopinionsauthor/'.$this->page.'/'.$authorID,
                    true
                );
            }

            if (!empty($opinions)) {
                foreach ($opinions as &$opinion) {
                    $opinion->author_name_slug  = $authorName;
                    // Overload opinion uri on opinion Object

                    $opinion->uri = $this->generateUrl(
                        'frontend_opinion_external_show_with_author_slug',
                        array(
                            'opinion_id'    => date('YmdHis', strtotime($opinion->created)).$opinion->id,
                            'author_name'   => $authorName,
                            'opinion_title' => $opinion->slug,
                        )
                    );

                    $opinion->author_uri = $this->generateUrl(
                        'frontend_opinion_external_author_frontpage',
                        array(
                            'author_id' => $authorID,
                            'author_slug' => $authorName,
                        )
                    );

                    $opinion = (array)$opinion; // template dependency
                }
            }

            $itemsPerPage = s::get('items_per_page');
            // Get external media url for author images
            $externalMediaUrl = $this->cm->getUrlContent($wsUrl.'/ws/instances/mediaurl/', true);

            $pagination = \Pager::factory(
                array(
                    'mode'        => 'Sliding',
                    'perPage'     => $itemsPerPage,
                    'append'      => false,
                    'path'        => '',
                    'delta'       => 4,
                    'clearIfVoid' => true,
                    'urlVar'      => 'page',
                    'totalItems'  => $countOpinions,
                    'fileName'    => $this->generateUrl(
                        'frontend_opinion_external_author_frontpage',
                        array(
                            'author_id' => $authorID,
                            'author_slug' => $authorName,
                        )
                    ).'/?page=%d',
                )
            );

            // Clean weird variables from this assign (must check
            // all the templates)
            // pagination_list change to pagination
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
                    'ext'             => $externalMediaUrl,
                )
            );

        } // End if isCached

        $this->getAds();

        return $this->render(
            'opinion/opinion_author_index.tpl',
            array('cache_id' => $cacheID)
        );
    }

    /**
     * Displays an opinion given its id
     *
     * @param Request $request the request object
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
            || !$this->view->isCached('opinion/opinion.tpl', $cacheID)
        ) {

            $this->view->assign('contentId', $opinionID);

            $author = new \Author($opinion->fk_author);
            $author->get_author_photos();
            $opinion->author = $author;

            // Rescato esta asignación para que genere correctamente el enlace a frontpage de opinion
            $opinion->author_name_slug = \StringUtils::get_title($opinion->name);

            // Fetch suggested contents
            $objSearch = \cSearch::getInstance();
            $suggestedContents = $objSearch->searchSuggestedContents(
                $opinion->metadata,
                'Opinion',
                " contents.available=1 AND pk_content = pk_fk_content",
                4
            );

            // Associated media code --------------------------------------
            if (isset($opinion->img2) && ($opinion->img2 > 0)) {
                $photo = new \Photo($opinion->img2);
                $this->view->assign('photo', $photo);
            }
            // Get author slug for suggested opinions
            foreach ($suggestedContents as &$suggest) {
                $element = new \Opinion($suggest['pk_content']);
                if (!empty($element->author)) {
                    $suggest['author_name'] = $element->author;
                    $suggest['author_name_slug'] = \StringUtils::get_title($element->author);
                } else {
                    $suggest['author_name_slug'] = "author";
                }
                $suggest['uri'] = $element->uri;

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

            $otherOpinions = $this->cm->find(
                'Opinion',
                $where.' AND `pk_opinion` <>' .$opinionID
                .' AND available = 1  AND content_status=1',
                ' ORDER BY created DESC LIMIT 0,9'
            );

            foreach ($otherOpinions as &$otOpinion) {
                $otOpinion->author = $author;
                $otOpinion->author_name_slug  = $opinion->author_name_slug;
                $otOpinion->uri  = $otOpinion->uri;
            }

            $this->view->assign(
                array(
                    'opinion'         => $opinion,
                    'content'         => $opinion,
                    'other_opinions'  => $otherOpinions,
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
     * Displays an external opinion given its id
     *
     * @param Request $request the request object
     *
     * @return Response the response object
     **/
    public function extShowAction(Request $request)
    {
        // Fetch HTTP params
        $dirtyID       = $request->query->getDigits('opinion_id');

        // Redirect to opinion frontpage if opinion_id wasn't provided
        if (empty($dirtyID)) {
            return new RedirectResponse($this->generateUrl('frontend_opinion_frontpage'));
        }

        // Getting Synchronize setting params
        $wsUrl = '';
        $syncParams = s::get('sync_params');
        foreach ($syncParams as $siteUrl => $categoriesToSync) {
            foreach ($categoriesToSync as $value) {
                if (preg_match('/'.$this->category_name.'/i', $value)) {
                    $wsUrl = $siteUrl;
                }
            }
        }

        $cacheID = $this->view->generateCacheId('sync'.$this->category_name, null, $dirtyID);

        if (($this->view->caching == 0) || !$this->view->isCached('opinion.tpl', $cacheID)) {

            $opinion = $this->cm->getUrlContent($wsUrl.'/ws/opinions/complete/'.$dirtyID, true);
            $opinion = unserialize($opinion);

            // Overload opinion object with category_name (used on ext_print)
            $opinion->category_name = $this->category_name;

            //Fetch information for Advertisements
            $this->getAds('inner');

            if (($opinion->available==1) && ($opinion->in_litter == 0)) {

                if (isset($opinion->img2) && ($opinion->img2 > 0)) {
                    $photo = new \Photo($opinion->img2);
                    $this->view->assign('photo', $photo);
                }

                $this->view->assign(
                    array(
                        'other_opinions'  => $opinion->otherOpinions,
                        'suggested'       => $opinion->machineRelated,
                        'opinion'         => $opinion,
                        'content'         => $opinion,
                        'actual_category' => 'opinion',
                        'media_url'       => $opinion->externalMediaUrl,
                        'contentId'       => $opinion->id, // Used on comments
                        'ext'             => 1 //Used on widgets
                    )
                );

            } else {
                throw new \Symfony\Component\Routing\Exception\ResourceNotFoundException();
            }
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
     * @param string $context the context to fetch ads from
     */
    private function getAds($context = 'frontpage')
    {
        if ($context == 'inner') {
            $positions = array(701, 702, 703, 704, 705, 706, 707, 708, 709, 710, 791, 792, 793);
            $intersticialId = 750;
        } else {
            $positions = array(601, 602, 603, 605, 609, 610, 691, 692);
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
