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
    }

    /**
     * Renders the opinion frontpage
     *
     * @return Response the response object
     **/
    public function frontpageAction()
    {
        $this->page = $this->request->query->getDigits('page', 1);

        // Index frontpage
        $cacheID = $this->view->generateCacheId($this->category_name, '', $this->page);

        // Don't execute the app logic if there are caches available
        if (($this->view->caching == 0)
            || !$this->view->isCached('opinion/opinion_frontpage.tpl', $cacheID)
        ) {
            $filters = [
                'content_status' => [['value' => 1]],
                'in_litter'      => [['value' => 0]],
            ];
            $em = $this->get('opinion_repository');

            if ($this->page == 1) {
                $order['in_home']   = 'DESC';
                $order['position']  = 'ASC';
                $order['starttime'] = 'DESC';
                $filters['in_home'] = [['value' => 1]];
            } else {
                $order['in_home']   = 'DESC';
                $order['starttime'] = 'DESC';
            }

            // Fetch configurations for this frontpage
            $configurations = $this->get('setting_repository')
                ->get(
                    'opinion_settings',
                    [
                        'total_editorial' => 2,
                        'total_director'  => 1,
                    ]
                );

            // Fetch last editorial opinions from editorial
            $editorial = array();
            if ($configurations['total_editorial'] > 0) {
                $ef = array_merge(
                    $filters,
                    ['type_opinion' => [['value' => 1]]]
                );

                $editorialContents = $em->findBy($ef, $order, $configurations['total_editorial'], $this->page);

                foreach ($editorialContents as &$opinion) {
                    if (isset($opinion->img1) && ($opinion->img1 > 0)) {
                        $opinion->img1 = $this->get('entity_repository')->find('Photo', $opinion->img1);
                    }
                }
                $this->view->assign('editorial', $editorialContents);
            }

            // Fetch last opinions from director
            $directorContents = array();
            if (!empty($configurations['total_director'])) {
                $ef = array_merge($filters, ['type_opinion' => [['value' => 2]]]);

                $directorContents = $em->findBy($ef, $order, $configurations['total_director'], $this->page);
                if (count($directorContents) > 0) {
                    $directorAuthor = $this->get('user_repository')->find($directorContents[0]->fk_author);
                }
            }

            foreach ($directorContents as &$opinion) {
                // Fetch the photo image of the director
                if (!empty($directorAuthor)) {
                    $director[0]->img1 = $this->get('entity_repository')->find('Photo', $directorContents[0]->img1);
                    if (isset($directorAuthor->photo->path_file)) {
                        $dir['photo'] = $directorAuthor->photo->path_file;
                    }
                    $dir['name'] = $directorAuthor->name;
                }
                var_dump($directorAuthor, $directorContents);die();
                if (isset($item->img1) && ($item->img1 > 0)) {
                    $director[0]->img1 = $this->get('entity_repository')->find('Photo', $item->img1);
                }

                $this->view->assign(
                    array(
                        'dir'              => $dir,
                        'director'         => $directorContents[0],
                        'opinionsDirector' => $directorContents
                    )
                );
            }

            $numOpinions  = s::get('items_per_page');
            if (!empty($configurations) && array_key_exists('total_opinions', $configurations)) {
                $numOpinions = $configurations['total_opinions'];
            }
             // Fetch all authors
            $allAuthors = \User::getAllUsersAuthors();

            $authorsBlog = array();
            foreach ($allAuthors as $authorData) {
                if ($authorData->is_blog == 1) {
                    $authorsBlog[$authorData->id] = $authorData;
                }
            }

            if (!empty($authorsBlog)) {
                // Must drop the blogs
                $filters = array_merge(
                    $filters,
                    array('opinions`.`fk_author'  => array(
                        array('value' => array_keys($authorsBlog), 'operator' => 'NOT IN'))
                    )
                );
            }

            $of = array_merge(
                $filters,
                array('type_opinion' => array(array('value' => 0)))
            );

            $opinions  = $em->findBy($of, $order, $numOpinions, $this->page);

            if ($this->page == 1) {
                // Make pagination using all opinions. Overwriting filter
                $of = array_merge(
                    $of,
                    array('in_home' => array(array('value' => array(0,1), 'operator' => 'IN' )))
                );

            }
            $countOpinions = $em->countBy($of);

            $pagination = $this->get('paginator')->create([
                'elements_per_page' => $numOpinions,
                'total_items'       => $countOpinions,
                'delta'             => 3,
                'base_url'          => $this->generateUrl('frontend_opinion_frontpage'),
            ]);

            $authors = array();
            $opinionsResult = array();
            foreach ($opinions as $opinion) {
                if (!array_key_exists($opinion->fk_author, $authors)) {
                    $author = $this->get('user_repository')->find($opinion->fk_author);

                    if (!is_object($author)) {
                        $author = new \User();
                    }

                    $authors[$opinion->fk_author] = $author;
                } else {
                    $author = $authors[$opinion->fk_author];
                }

                if (empty($author->meta)
                    || !array_key_exists('is_blog', $author->meta)
                    || $author->meta['is_blog'] == 0
                ) {
                    $opinion->author           = $authors[$opinion->fk_author];
                    $opinion->name             = $opinion->author->name;
                    $opinion->author_name_slug = \Onm\StringUtils::getTitle($opinion->name);
                    $item = new \Content();
                    $item->loadAllContentProperties($opinion->pk_content);
                    $opinion->summary = $item->summary;
                    $opinion->img1_footer = $item->img1_footer;
                    if (isset($item->img1) && ($item->img1 > 0)) {
                        $opinion->img1 = $this->get('entity_repository')->find('Photo', $item->img1);
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
                'x-tags'          => 'opinion_frontpage,'.$this->page,
                'x-cache-for'     => '1d'
            )
        );
    }

    /**
     * Renders the opinion frontpage
     *
     * @return Response the response object
     **/
    public function extFrontpageAction()
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
            || !$this->view->isCached('opinion/opinion_frontpage.tpl', $cacheID)
        ) {
            // Get sync params
            $wsUrl = '';
            $syncParams = s::get('sync_params');
            if ($syncParams) {
                foreach ($syncParams as $siteUrl => $values) {
                    if (in_array($categoryName, $values['categories'])) {
                        $wsUrl = $siteUrl;
                    }
                }
            }

            $this->cm = new \ContentManager();

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
            $totalOpinions =  ITEMS_PAGE + (int) $this->cm->getUrlContent(
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
                $opinion->author_name_slug = \Onm\StringUtils::getTitle($opinion->name);
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

            $pagination = $this->get('paginator')->create([
                'elements_per_page' => $itemsPerPage,
                'total_items'       => $totalOpinions,
                'delta'             => 3,
                'base_url'          => $this->generateUrl('frontend_opinion_external_frontpage'),
            ]);

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
            || !$this->view->isCached('opinion/opinion_author_index.tpl', $cacheID)
        ) {
            // Get author info
            $author = $this->get('user_repository')->find($authorID);
            if (is_null($author)) {
                 return new RedirectResponse($this->generateUrl('frontend_opinion_frontpage'));
            }

            // WTF is this!!!!?
            $author->params = $author->meta;
            $author->slug   = $author->username;

            if (array_key_exists('is_blog', $author->params) && $author->params['is_blog'] == 1) {
                return new RedirectResponse(
                    $this->generateUrl('frontend_blog_author_frontpage', array('author_slug' => $author->username))
                );
            }

            // Setting filters for the further SQLs
            if ($author->id == 1 && $author->slug == 'editorial') {
                // Editorial
                $filter = [ 'type_opinion' => [['value' => 1]] ];
                $this->view->assign('actual_category', 'editorial');
            } elseif ($author->id == 2 && $author->slug == 'director') {
                // Director
                $filter = [ 'type_opinion' => [['value' => 2]] ];
            } else {
                // Regular authors
                $filter = [
                    'type_opinion' => [['value' => 0]],
                    'opinions`.`fk_author' => [['value' => $author->id]]
                ];

                $author->slug = \Onm\StringUtils::getTitle($author->name);
                $this->view->assign('actual_category', 'opinion');
            }

            $filters      = array_merge([ 'content_status' => [['value' => 1]], ], $filter);
            $orderBy      = ['created' => 'DESC'];
            $itemsPerPage = s::get('items_per_page');

            // Get the number of total opinions for this author for pagination purpouses
            $countOpinions = $this->get('opinion_repository')->countBy(
                array_merge([ 'content_type_name' => [['value' => 'opinion']]], $filters)
            );

            $opinions = $this->get('opinion_repository')->findBy($filters, $orderBy, $itemsPerPage);

            foreach ($opinions as &$opinion) {
                $item = array (
                    'pk_content'       => $opinion->pk_content,
                    'position'         => $opinion->position,
                    'avatar_img_id'    => $opinion->avatar_img_id,
                    'title'            => $opinion->title,
                    'slug'             => $opinion->slug,
                    'type_opinion'     => $opinion->type_opinion,
                    'body'             => $opinion->body,
                    'changed'          => $opinion->changed,
                    'created'          => $opinion->created,
                    'with_comment'     => $opinion->with_comment,
                    'starttime'        => $opinion->starttime,
                    'endtime'          => $opinion->endtime,
                    'id'               => $opinion->id,
                    'name'             => $author->name,
                    'bio'              => $author->bio,
                    'path_img'         => \Photo::getPhotoPath($author->avatar_img_id),
                    'summary'          => $opinion->summary,
                    'img1_footer'      => $opinion->img1_footer,
                    'pk_author'        => $author->id,
                    'author_name_slug' => $author->slug,
                    'comments'         => $opinion->comments,
                    'uri'              => $this->generateUrl(
                        'frontend_opinion_show_with_author_slug',
                        array(
                            'opinion_id'    => date('YmdHis', strtotime($opinion->created)).$opinion->id,
                            'author_name'   => $author->slug,
                            'opinion_title' => $opinion->slug,
                        )
                    ),
                    'author_uri'       => $this->generateUrl(
                        'frontend_opinion_author_frontpage',
                        array(
                            'author_id'   => sprintf('%06d', $author->id),
                            'author_slug' => $author->name,
                        )
                    ),
                );

                if (isset($item->img1) && ($item->img1 > 0)) {
                    $opinion['img1'] = $this->get('entity_repository')->find('Photo', $item->img1);
                } elseif (isset($item->img2) && ($item->img2 > 0)) {
                    $opinion['img1'] = $this->get('entity_repository')->find('Photo', $item->img2);
                }

                $opinion = $item;
            }

            $pagination = $this->get('paginator')->create([
                'elements_per_page' => $itemsPerPage,
                'total_items'       => $countOpinions,
                'base_url'          => $this->generateUrl(
                    'frontend_opinion_author_frontpage',
                    array(
                        'author_id' => sprintf('%06d', $author->id),
                        'author_slug' => $author->slug,
                    )
                ),
            ]);

            $this->view->assign(
                array(
                    'pagination' => $pagination,
                    'opinions'   => $opinions,
                    'author'     => $author,
                    'page'       => $this->page,
                )
            );

        }

        // Fetch information for Advertisements
        $ads = $this->getAds();
        $this->view->assign('advertisements', $ads);

        return $this->render(
            'opinion/opinion_author_index.tpl',
            array(
                'cache_id'        => $cacheID,
                'actual_category' => 'opinion',
                'x-tags'          => 'author_frontpage,'.$this->page,
                'x-cache-for'     => '1d'
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
            || !$this->view->isCached('opinion/opinion_author_index.tpl', $cacheID)
        ) {
            // Get sync params
            $wsUrl = '';
            $syncParams = s::get('sync_params');
            if ($syncParams) {
                foreach ($syncParams as $siteUrl => $values) {
                    if (in_array($categoryName, $values['categories'])) {
                        $wsUrl = $siteUrl;
                    }
                }
            }

            $this->cm = new \ContentManager();

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

            $this->cm = new \ContentManager();

            $itemsPerPage = s::get('items_per_page');
            // Get external media url for author images
            $externalMediaUrl = $this->cm->getUrlContent($wsUrl.'/ws/instances/mediaurl/', true);

            $pagination = $this->get('paginator')->create([
                'elements_per_page' => $itemsPerPage,
                'total_items'       => $countOpinions,
                'base_url'          => $this->generateUrl(
                    'frontend_opinion_external_author_frontpage',
                    array(
                        'author_id' => sprintf('%06d', $author->id),
                        'author_slug' => $author->slug,
                    )
                ),
            ]);

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

        // Resolve article ID
        $er = $this->get('entity_repository');
        $opinionID = $er->resolveID($dirtyID);


        // Redirect to opinion frontpage if opinion_id wasn't provided
        if (empty($opinionID)) {
            return new RedirectResponse($this->generateUrl('frontend_opinion_frontpage'));
        }

        $opinion = $er->find('Opinion', $opinionID);

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

            $author = $this->get('user_repository')->find($opinion->fk_author);
            $opinion->author = $author;

            if (is_object($author)
                && is_array($author->meta)
                && array_key_exists('is_blog', $author->meta)
                && $author->meta['is_blog'] == 1
            ) {
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
            $opinion->author_name_slug = \Onm\StringUtils::getTitle($opinion->name);

            // Machine suggested contents code -----------------------------
            $machineSuggestedContents = $this->get('automatic_contents')->searchSuggestedContents(
                'opinion',
                " pk_content <>".$opinion->id,
                4
            );

            // Get author slug for suggested opinions
            foreach ($machineSuggestedContents as &$suggest) {
                $element = $er->find('Opinion', $suggest['pk_content']);
                if (!empty($element->author)) {
                    $suggest['author_name'] = $element->author;
                    $suggest['author_name_slug'] = \Onm\StringUtils::getTitle($element->author);
                } else {
                    $suggest['author_name_slug'] = "author";
                }
                $suggest['uri'] = $element->uri;
            }

            $this->view->assign('suggested', $machineSuggestedContents);

            // Associated media code --------------------------------------
            if (isset($opinion->img2) && ($opinion->img2 > 0)) {
                $photo = $er->find('Photo', $opinion->img2);
                $this->view->assign('photo', $photo);
            }

            // Fetch the other opinions for this author
            if ($opinion->type_opinion == 1) {
                $where =' opinions.type_opinion = 1';
                $opinion->name = 'Editorial';
                $opinion->author_name_slug = \StringUtils::getTitle($opinion->name);
                $this->view->assign('actual_category', 'editorial');
            } elseif ($opinion->type_opinion == 2) {
                $where =' opinions.type_opinion = 2';
                $opinion->name = 'Director';
                $opinion->author_name_slug = \StringUtils::getTitle($opinion->name);
            } else {
                $where =' opinions.fk_author='.($opinion->fk_author);
            }

            $this->cm = new \ContentManager();

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
                'x-tags'          => 'opinion,'.$opinionID,
                'x-cache-for' => '1d'
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

        // Get sync params
        $wsUrl = '';
        $syncParams = s::get('sync_params');
        if ($syncParams) {
            foreach ($syncParams as $siteUrl => $values) {
                if (in_array($categoryName, $values['categories'])) {
                    $wsUrl = $siteUrl;
                }
            }
        }

        $cacheID = $this->view->generateCacheId('sync'.$this->category_name, null, $dirtyID);

        if (($this->view->caching == 0)
            || !$this->view->isCached('opinion/opinion.tpl', $cacheID)
        ) {
            $this->cm = new \ContentManager();

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
