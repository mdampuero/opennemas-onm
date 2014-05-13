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
namespace Frontend\Controller;

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
            $orderBy='ORDER BY contents.in_home DESC, position ASC, starttime DESC ';
        } else {
            $where = 'AND contents.in_home=0 ';
            $orderBy='ORDER BY starttime DESC ';
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
                    'AND contents.content_status=1 '.$where,
                    $orderBy.
                    'LIMIT '.$totalEditorial
                );
            }

            foreach ($editorial as &$op) {
                $item = new \Content();
                $item->loadAllContentProperties($op->pk_content);

                $op->summary = $item->summary;
                $op->img1_footer = $item->img1_footer;
                if (isset($item->img1) && ($item->img1 > 0)) {
                    $op->img1 = new \Photo($item->img1);
                }
            }

            // Fetch last opinions from director
            $director = array();
            if (!empty($totalDirector)) {
                $director = $this->cm->find(
                    'Opinion',
                    'opinions.type_opinion=2 '.
                    'AND contents.content_status=1 '.$where,
                    $orderBy.
                    ' LIMIT '.$totalDirector
                );
            }

            if (isset($director) && !empty($director)) {
                // Fetch the photo image of the director
                $aut = new \User($director[0]->fk_author);
                if (isset($aut->photo->path_file)) {
                    $dir['photo'] = $aut->photo->path_file;
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
             // Fetch all authors
            $allAuthors = \User::getAllUsersAuthors();

            $authorsBlog = array();
            foreach ($allAuthors as $authorData) {
                if ($authorData->is_blog == 1) {
                    $authorsBlog[$authorData->id] = $authorData;
                }
            }
            if (!empty($authorsBlog)) {
                $where .= ' AND opinions.fk_author NOT IN ('.implode(', ', array_keys($authorsBlog)).") ";
            }

            list($countOpinions, $opinions)= $this->cm->getCountAndSlice(
                'Opinion',
                null,
                'opinions.type_opinion=0 '.
                $where.
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
            $opinionsResult = array();
            foreach ($opinions as $opinion) {
                if (!array_key_exists($opinion->fk_author, $authors)) {
                    $author = new \User($opinion->fk_author);
                    $authors[$opinion->fk_author] = $author;
                } else {
                    $author = $authors[$opinion->fk_author];
                }
                if (!array_key_exists('is_blog', $author->meta) || $author->meta['is_blog'] != 1) {
                    $opinion->author           = $authors[$opinion->fk_author];
                    $opinion->name             = $opinion->author->name;
                    $opinion->author_name_slug = \Onm\StringUtils::get_title($opinion->name);
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
                            'slug' => $opinion->author->username,
                            'id'   => sprintf('%06d', $opinion->author->id)
                        )
                    );
                    $opinionsResult[] = $opinion;
                }
            }

            $this->view->assign(
                array(
                    'editorial'  => $editorial,
                    'opinions'   => $opinionsResult,
                    'authors'    => $authors,
                    'pagination' => $pagination,
                    'page'       => $this->page
                )
            );
        }

        $ads = $this->getAds();
        $this->view->assign('advertisements', $ads);

        return $this->render(
            'opinion/opinion_frontpage.tpl',
            array(
                'cache_id'        => $cacheID,
                'actual_category' => 'opinion',
            )
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
                $aut = $this->cm->getUrlContent(
                    $wsUrl.'/ws/authors/id/'.$director[0]->fk_author,
                    true
                );

                if (isset($aut->photo->path_file)) {
                    $dir['photo'] = $aut->photo->path_file;
                }

                $dir['name'] = $aut->name;
                $this->view->assign('dir', $dir);
                $this->view->assign('director', $director[0]);
            }

            if ($this->page == 1) {
                $opinions = $this->cm->getUrlContent($wsUrl.'/ws/opinions/authorsinhome/', true);
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
                $opinion->author_name_slug = \Onm\StringUtils::get_title($opinion->name);
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
            array(
                'cache_id'        => $cacheID,
                'actual_category' => 'opinion',
            )
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
        $authorID = $request->query->getDigits('author_id', null);

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
            $author = new \User($authorID);
            $author->params = $author->getMeta();
            $author->slug   = strtolower(
                $request->query->filter('author_slug', null, FILTER_SANITIZE_STRING)
            );

            if (array_key_exists('is_blog', $author->params) && $author->params['is_blog'] == 1) {
                return new RedirectResponse(
                    $this->generateUrl('frontend_blog_author_frontpage', array('author_slug' => $author->username))
                );
            }

            // Setting filters for the further SQLs
            if ($author->id == 1 && $author->slug == 'editorial') {
                // Editorial
                $filter = 'opinions.type_opinion=1';
                $this->view->assign('actual_category', 'editorial');
            } elseif ($author->id == 2 && $author->slug == 'director') {
                // Director
                $filter =  'opinions.type_opinion=2';
            } else {
                // Regular authors
                $filter = 'opinions.type_opinion=0 AND opinions.fk_author='.$author->id;
                $author->slug = \Onm\StringUtils::get_title($author->name);
                $this->view->assign('actual_category', 'opinion');
            }

            // generate pagination params
            $itemsPerPage = s::get('items_per_page');
            $_limit = ' LIMIT '.(($this->page-1)*$itemsPerPage).', '.($itemsPerPage);

            // Get the number of total opinions for this author for pagination purpouses
            $countOpinions = $this->cm->cache->count(
                'Opinion',
                $filter
                .' AND contents.content_status=1 '
            );

            // Get the list articles for this author
            $opinions = $this->cm->getOpinionArticlesWithAuthorInfo(
                $filter
                .' AND contents.content_status=1',
                'ORDER BY created DESC '.$_limit
            );

            if (!empty($opinions)) {
                foreach ($opinions as &$opinion) {
                    // Overload opinion array with more information
                    $item = new \Content();
                    $item->loadAllContentProperties($opinion['pk_content']);
                    $opinion['summary'] = $item->summary;
                    $opinion['img1_footer'] = $item->img1_footer;
                    $opinion['pk_author'] = $author->id;
                    $opinion['author_name_slug']  = $author->slug;
                    $opinion['comments']  = $item->comments;
                    if (isset($item->img1) && ($item->img1 > 0)) {
                        $opinion['img1'] = new \Photo($item->img1);
                    }

                    // Generate opinion uri
                    $opinion['uri'] = $this->generateUrl(
                        'frontend_opinion_show_with_author_slug',
                        array(
                            'opinion_id'    => date('YmdHis', strtotime($opinion['created'])).$opinion['id'],
                            'author_name'   => $opinion['author_name_slug'],
                            'opinion_title' => $opinion['slug'],
                        )
                    );

                    // Generate author uri
                    $opinion['author_uri'] = $this->generateUrl(
                        'frontend_opinion_author_frontpage',
                        array(
                            'author_id'   => sprintf('%06d', $opinion['pk_author']),
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
                            'author_id' => sprintf('%06d', $author->id),
                            'author_slug' => $author->slug,
                        )
                    ).'/?page=%d',
                )
            );

            $this->view->assign(
                array(
                    'pagination' => $pagination,
                    'opinions'   => $opinions,
                    'author'     => $author,
                    'page'       => $this->page,
                )
            );

        } // End if isCached

        //Fetch information for Advertisements
        $ads = $this->getAds();
        $this->view->assign('advertisements', $ads);

        return $this->render(
            'opinion/opinion_author_index.tpl',
            array(
                'cache_id'        => $cacheID,
                'actual_category' => 'opinion',
            )
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
            $author->slug = strtolower($authorSlug);

            // Setting filters for the further SQLs
            if ($author->id == 1 && $authorSlug == 'editorial') {
                // Editorial
                $countOpinions = $this->cm->getUrlContent(
                    $wsUrl.'/ws/opinions/counteditorialopinions/',
                    true
                );
                $opinions = $this->cm->getUrlContent(
                    $wsUrl.'/ws/opinions/allopinionseditorial/'.$this->page,
                    true
                );
            } elseif ($author->id == 2 && $author->slug == 'director') {
                // Director
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
                $countOpinions = $this->cm->getUrlContent(
                    $wsUrl.'/ws/opinions/countauthoropinions/'.$author->id,
                    true
                );
                $opinions = $this->cm->getUrlContent(
                    $wsUrl.'/ws/opinions/allopinionsauthor/'.$this->page.'/'.$author->id,
                    true
                );
            }

            if (!empty($opinions)) {
                foreach ($opinions as &$opinion) {
                    $opinion->author_name_slug  = $author->slug;
                    // Overload opinion uri on opinion Object

                    $opinion->uri = $this->generateUrl(
                        'frontend_opinion_external_show_with_author_slug',
                        array(
                            'opinion_id'    => date('YmdHis', strtotime($opinion->created)).$opinion->id,
                            'author_name'   => $author->slug,
                            'opinion_title' => $opinion->slug,
                        )
                    );

                    $opinion->author_uri = $this->generateUrl(
                        'frontend_opinion_external_author_frontpage',
                        array(
                            'author_id' => sprintf('%06d', $author->id),
                            'author_slug' => $author->slug,
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
                            'author_id' => sprintf('%06d', $author->id),
                            'author_slug' => $author->slug,
                        )
                    ).'/?page=%d',
                )
            );

            $this->view->assign(
                array(
                    'pagination' => $pagination,
                    'opinions'   => $opinions,
                    'author'     => $author,
                    'page'       => $this->page,
                    'ext'        => $externalMediaUrl,
                )
            );

        } // End if isCached

        $this->getAds();

        return $this->render(
            'opinion/opinion_author_index.tpl',
            array(
                'cache_id'        => $cacheID,
                'actual_category' => 'opinion',
            )
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
        $dirtyID   = $request->query->getDigits('opinion_id');
        $opinionID = \Content::resolveID($dirtyID);

        // Redirect to opinion frontpage if opinion_id wasn't provided
        if (empty($opinionID)) {
            return new RedirectResponse($this->generateUrl('frontend_opinion_frontpage'));
        }

        $opinion = new \Opinion($opinionID);

        // TODO: Think that this comments related code can be deleted.
        if (($opinion->content_status != 1) || ($opinion->in_litter != 0)) {
            throw new \Symfony\Component\Routing\Exception\ResourceNotFoundException();
        }

        //Fetch information for Advertisements
        $ads = $this->getAds('inner');
        $this->view->assign('advertisements', $ads);

        // Don't execute the app logic if there are caches available
        $cacheID = $this->view->generateCacheId($this->category_name, '', $opinionID);
        if (($this->view->caching == 0)
            || !$this->view->isCached('opinion/opinion.tpl', $cacheID)
        ) {

            $this->view->assign('contentId', $opinionID);

            $author = new \User($opinion->fk_author);
            $opinion->author = $author;

            if (array_key_exists('is_blog', $author->meta) && $author->meta['is_blog'] == 1) {
                return new RedirectResponse(
                    $this->generateUrl(
                        'frontend_blog_show',
                        array(
                            'blog_id' => $dirtyID,
                            'author_name' => $author->username,
                            'blog_title'  => $opinion->slug,
                        )
                    )
                );
            }

            // Rescato esta asignación para que genere correctamente el enlace a frontpage de opinion
            $opinion->author_name_slug = \Onm\StringUtils::get_title($opinion->name);

            // Machine suggested contents code -----------------------------
            $machineSuggestedContents = $this->get('automatic_contents')->searchSuggestedContents(
                $opinion->metadata,
                'opinion',
                " contents.content_status=1 AND pk_content = pk_fk_content",
                4
            );

            // Get author slug for suggested opinions
            foreach ($machineSuggestedContents as &$suggest) {
                $element = new \Opinion($suggest['pk_content']);
                if (!empty($element->author)) {
                    $suggest['author_name'] = $element->author;
                    $suggest['author_name_slug'] = \Onm\StringUtils::get_title($element->author);
                } else {
                    $suggest['author_name_slug'] = "author";
                }
                $suggest['uri'] = $element->uri;
            }

            $this->view->assign('suggested', $machineSuggestedContents);

            // Associated media code --------------------------------------
            if (isset($opinion->img2) && ($opinion->img2 > 0)) {
                $photo = new \Photo($opinion->img2);
                $this->view->assign('photo', $photo);
            }

            // Fetch the other opinions for this author
            if ($opinion->type_opinion == 1) {
                $where =' opinions.type_opinion = 1';
                $opinion->name = 'Editorial';
                $this->view->assign('actual_category', 'editorial');
            } elseif ($opinion->type_opinion == 2) {
                $where =' opinions.type_opinion = 2';
                $opinion->name = 'Director';
            } else {
                $where =' opinions.fk_author='.($opinion->fk_author);
            }

            $otherOpinions = $this->cm->find(
                'Opinion',
                $where.' AND `pk_opinion` <>' .$opinionID
                .' AND content_status=1',
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
            array(
                'cache_id'        => $cacheID,
                'actual_category' => 'opinion',
            )
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
        $dirtyID = $request->query->getDigits('opinion_id');

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
            $ads = $this->getAds('inner');
            $this->view->assign('advertisements', $ads);

            if (($opinion->content_status==1) && ($opinion->in_litter == 0)) {

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
            array(
                'cache_id'        => $cacheID,
                'actual_category' => 'opinion',
            )
        );
    }

    /**
     * Fetches the advertisement
     *
     * @param string $context the context to fetch ads from
     */
    public static function getAds($context = '')
    {
        // Get opinion positions
        $positionManager = getService('instance_manager')->current_instance->theme->getAdsPositionManager();
        if ($context == 'inner') {
            $positions = $positionManager->getAdsPositionsForGroup('opinion_inner', array(7, 9));
        } else {
            $positions = $positionManager->getAdsPositionsForGroup('opinion_frontpage', array(7, 9));
        }

        return \Advertisement::findForPositionIdsAndCategory($positions, '4');
    }
}
