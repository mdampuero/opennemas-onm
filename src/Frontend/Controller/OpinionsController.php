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
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Common\Core\Controller\Controller;
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
        $this->view->setConfig('opinion');

        $this->category_name = $this->request->query->filter('category_name', 'opinion', FILTER_SANITIZE_STRING);
        $this->view->assign('actual_category', 'opinion'); // Used in renderMenu
    }

    /**
     * Renders the opinion frontpage
     *
     * @return Response the response object
     */
    public function frontpageAction()
    {
        $page = $this->request->query->getDigits('page', 1);

        // Index frontpage
        $cacheID = $this->view->generateCacheId($this->category_name, '', $page);

        // Don't execute the app logic if there are caches available
        if (($this->view->getCaching() === 0)
            || !$this->view->isCached('opinion/opinion_frontpage.tpl', $cacheID)
        ) {
            $date = date('Y-m-d H:i:s');
            $filters = [
                'content_status' => [['value' => 1]],
                'in_litter'      => [['value' => 0]],
                'starttime' => [
                    'union' => 'OR',
                    [ 'value' => $date, 'operator' => '<' ],
                    [ 'value'  => null, 'operator' => 'IS', 'field' => true ],
                ],
                'endtime' => [
                    'union'   => 'OR',
                    [ 'value'  => null, 'operator' => 'IS', 'field' => true ],
                    [ 'value' => '0000-00-00 00:00:00', 'operator' => '=' ],
                    [ 'value' => $date, 'operator' => '>' ]
                ],
            ];

            $em = $this->get('opinion_repository');

            $order['in_home'] = 'DESC';
            if ($page == 1) {
                $order['position']  = 'ASC';
                $filters['in_home'] = [['value' => 1]];
            }
            $order['starttime'] = 'DESC';

            // Fetch configurations for this frontpage
            $configurations = $this->get('setting_repository')->get(
                'opinion_settings',
                [
                    'total_editorial' => 2,
                    'total_director'  => 1,
                ]
            );

            // Fetch last editorial opinions from editorial
            if ($configurations['total_editorial'] > 0) {
                $filters['type_opinion'] = [['value' => 1]];

                $editorialContents = $em->findBy($filters, $order, $configurations['total_editorial'], $page);

                foreach ($editorialContents as &$opinion) {
                    if (isset($opinion->img1) && ($opinion->img1 > 0)) {
                        $opinion->img1 = $this->get('entity_repository')
                            ->find('Photo', $opinion->img1);
                    }
                }

                $this->view->assign('editorial', $editorialContents);
            }

            // Fetch last opinions from director
            $contents = [];
            if (!empty($configurations['total_director'])) {
                $filters['type_opinion'] = [['value' => 2]];

                $contents = $em->findBy($filters, $order, 2, $page);

                if (count($contents) > 0) {
                    foreach ($contents as &$opinion) {
                        if (isset($item->img1) && ($item->img1 > 0)) {
                            $contents[0]->img1 = $this
                                ->get('entity_repository')
                                ->find('Photo', $item->img1);
                        }
                    }

                    $this->view->assign([
                        'director'         => $contents[0],
                        'opinionsDirector' => $contents
                    ]);
                }
            }

            $numOpinions  = $this->get('setting_repository')->get('items_per_page');
            if (!empty($configurations)
                && array_key_exists('total_opinions', $configurations)
            ) {
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

            $filters['type_opinion'] = [['value' => 0]];
            if (!empty($authorsBlog)) {
                // Must drop the blogs
                $filters = array_merge(
                    $filters,
                    array('opinions`.`fk_author'  => array(
                        array('value' => array_keys($authorsBlog), 'operator' => 'NOT IN'))
                    )
                );
            }

            // Make pagination using all opinions. Overwriting filter
            if ($page == 1) {
                unset($filters['in_home']);
            }

            $opinions      = $em->findBy($filters, $order, $numOpinions, $page);
            $countOpinions = $em->countBy($filters);

            $pagination = $this->get('paginator')->get([
                'directional' => true,
                'epp'         => $numOpinions,
                'page'        => $page,
                'total'       => $countOpinions,
                'route'       => 'frontend_opinion_frontpage'
            ]);

            $authors = [];
            $ur = $this->get('user_repository');
            foreach ($opinions as &$opinion) {
                if (!array_key_exists($opinion->fk_author, $authors)) {
                    $authors[$opinion->fk_author] = $ur->find($opinion->fk_author);
                }

                $opinion->author = $authors[$opinion->fk_author];

                if (isset($opinion->author)
                    && (empty($opinion->author->meta)
                    || !array_key_exists('is_blog', $opinion->author->meta)
                    || $opinion->author->meta['is_blog'] == 0)
                ) {
                    $opinion->name             = $opinion->author->name;
                    $opinion->author_name_slug =
                        \Onm\StringUtils::getTitle($opinion->name);

                    if (isset($opinion->img1) && ($opinion->img1 > 0)) {
                        $opinion->img1 = $this->get('entity_repository')
                            ->find('Photo', $opinion->img1);
                    }

                    $opinion->author->uri = \Uri::generate(
                        'opinion_author_frontpage',
                        [
                            'slug' => urlencode(\Onm\StringUtils::generateSlug($opinion->author->name)),
                            'id'   => sprintf('%06d', $opinion->author->id)
                        ]
                    );
                }
            }

            $this->view->assign([
                'opinions'   => $opinions,
                'authors'    => $authors,
                'pagination' => $pagination,
                'page'       => $page
            ]);
        }

        list($positions, $advertisements) = $this->getAds();
        $this->view->assign('ads_positions', $positions);
        $this->view->assign('advertisements', $advertisements);

        return $this->render(
            'opinion/opinion_frontpage.tpl',
            array(
                'cache_id'        => $cacheID,
                'actual_category' => 'opinion',
                'x-tags'          => 'opinion-frontpage,'.$page,
                'x-cache-for'     => '+1 day'
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
        $page = $this->request->query->getDigits('page', 1);
        $categoryName = 'opinion';

        // Index frontpage
        $cacheID = $this->view->generateCacheId($this->category_name, '', $page);

        // Don't execute the app logic if there are caches available
        if (($this->view->getCaching() === 0)
            || !$this->view->isCached('opinion/opinion_frontpage.tpl', $cacheID)
        ) {
            // Get sync params
            $wsUrl = '';
            $syncParams = s::get('sync_params');
            if ($syncParams) {
                foreach ($syncParams as $siteUrl => $values) {
                    if (is_array($values['categories'])
                        && in_array($categoryName, $values['categories'])
                    ) {
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

            if ($page == 1) {
                $opinions = $this->cm->getUrlContent($wsUrl.'/ws/opinions/authorsinhome/', true);
            } else {
                // Fetch last opinions of contributors and paginate them by ITEM_PAGE
                $opinions = $this->cm->getUrlContent($wsUrl.'/ws/opinions/authorsnotinhomepaged/'.$page, true);
            }

            // Sum of total opinions in home + not in home for the pagination
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

            $pagination = $this->get('paginator')->get([
                'directional' => true,
                'epp'         => $itemsPerPage,
                'page'        => $page,
                'total'       => $totalOpinions,
                'route'       => 'frontend_opinion_external_frontpage'
            ]);

            $this->view->assign(
                array(
                    'editorial'  => $editorial,
                    'opinions'   => $opinions,
                    'authors'    => $authors,
                    'pagination' => $pagination,
                    'page'       => $page,
                    'ext'        => $externalMediaUrl,
                )
            );
        }

        list($positions, $advertisements) = $this->getAds();

        return $this->render('opinion/opinion_frontpage.tpl', [
            'ads_positions'   => $positions,
            'advertisements'  => $advertisements,
            'cache_id'        => $cacheID,
            'actual_category' => 'opinion',
            'x-tags'          => 'ext-opinion-frontpage',
            'x-cache-for'     => '+1 day'
        ]);
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
        $page = $this->request->query->getDigits('page', 1);

        if (empty($authorID)) {
            throw new ResourceNotFoundException();
        }

        // Don't execute the app logic if there are caches available
        $cacheID = $this->view->generateCacheId($this->category_name, $authorID, $page);
        if (($this->view->getCaching() === 0)
            || !$this->view->isCached('opinion/opinion_author_index.tpl', $cacheID)
        ) {
            // Get author info
            $author = $this->get('user_repository')->find((int)$authorID);
            if (is_null($author)) {
                throw new ResourceNotFoundException();
            }

            if (array_key_exists('is_blog', $author->meta)
                && $author->params['is_blog'] == 1
            ) {
                return new RedirectResponse(
                    $this->generateUrl(
                        'frontend_blog_author_frontpage',
                        ['author_slug' => $author->username]
                    )
                );
            }

            // Setting filters for the further SQLs
            $date = date('Y-m-d H:i:s');
            $filters = [
                'content_status'    => [['value' => 1]],
                'content_type_name' => [['value' => 'opinion']],
                'starttime' => [
                    'union' => 'OR',
                    [ 'value' => null, 'operator' => 'IS' ],
                    [ 'value' => $date, 'operator' => '<' ]
                ],
                'endtime' => [
                    'union'   => 'OR',
                    [ 'value'  => null, 'operator'      => 'IS' ],
                    [ 'value' => '0000-00-00 00:00:00', 'operator' => '=' ],
                    [ 'value' => $date, 'operator' => '>' ]
                ],
            ];
            if ($author->id == 1 && $author->username == 'editorial') {
                // Editorial
                $filters['type_opinion'] = [['value' => 1]];
                $author->slug = 'editorial';
                $this->view->assign('actual_category', 'editorial');
            } elseif ($author->id == 2 && $author->username == 'director') {
                // Director
                $filters['type_opinion'] = [['value' => 2]];
                $author->slug = 'director';
            } else {
                // Regular authors
                $filters['type_opinion']         = [['value' => 0]];
                $filters['opinions`.`fk_author'] = [['value' => $author->id]];
                $author->slug = \Onm\StringUtils::getTitle($author->name);
            }

            $orderBy      = ['created' => 'DESC'];

            // Total opinions per page
            $numOpinions  = $this->get('setting_repository')->get('items_per_page');
            if (!empty($configurations)
                && array_key_exists('total_opinions', $configurations)
            ) {
                $numOpinions = $configurations['total_opinions'];
            }

            // Get the number of total opinions for this author for pagination purpouses
            $countOpinions = $this->get('opinion_repository')->countBy($filters);
            $opinions      = $this->get('opinion_repository')->findBy($filters, $orderBy, $numOpinions, $page);

            foreach ($opinions as &$opinion) {
                // Get author uri
                $opinion->author_uri = $this->generateUrl(
                    'frontend_opinion_author_frontpage',
                    [
                        'author_id'   => sprintf('%06d', $author->id),
                        'author_slug' => $author->slug,
                    ]
                );

                // Get opinion image
                if (isset($opinion->img1) && ($opinion->img1 > 0)) {
                    $opinion->img1 = $this->get('entity_repository')->find('Photo', $opinion->img1);
                }
            }

            $pagination = $this->get('paginator')->get([
                'directional' => true,
                'epp'         => $numOpinions,
                'page'        => $page,
                'total'       => $countOpinions,
                'route'       => [
                    'name'   => 'frontend_opinion_author_frontpage',
                    'params' => [
                        'author_id'   => sprintf('%06d', $author->id),
                        'author_slug' => $author->slug,
                    ]
                ]
            ]);

            $this->view->assign(
                array(
                    'pagination' => $pagination,
                    'opinions'   => $opinions,
                    'author'     => $author,
                    'page'       => $page,
                )
            );
        }

        // Fetch information for Advertisements
        list($positions, $advertisements) = $this->getAds();

        return $this->render('opinion/opinion_author_index.tpl', [
            'ads_positions'   => $positions,
            'advertisements'  => $advertisements,
            'cache_id'        => $cacheID,
            'actual_category' => 'opinion',
            'x-tags'          => 'author-frontpage,'.$authorID.','.$page,
            'x-cache-for'     => '+1 day'
        ]);
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
        $authorID     = $request->query->getDigits('author_id', null);
        $authorSlug   = $request->query->filter('author_slug', null, FILTER_SANITIZE_STRING);
        $page         = $this->request->query->getDigits('page', 1);
        $categoryName = 'opinion';

        if (empty($authorID)) {
            return new RedirectResponse($this->generateUrl('frontend_opinion_frontpage'));
        }

        // Author frontpage
        $cacheID = $this->view->generateCacheId($this->category_name, $authorID, $page);
        // Don't execute the app logic if there are caches available
        if (($this->view->getCaching() === 0)
            || !$this->view->isCached('opinion/opinion_author_index.tpl', $cacheID)
        ) {
            // Get sync params
            $wsUrl = '';
            $syncParams = s::get('sync_params');
            if ($syncParams) {
                foreach ($syncParams as $siteUrl => $values) {
                    if (is_array($values['categories'])
                        && in_array($categoryName, $values['categories'])
                    ) {
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
                    $wsUrl.'/ws/opinions/allopinionseditorial/'.$page,
                    true
                );
            } elseif ($author->id == 2 && $author->slug == 'director') {
                // Director
                $countOpinions = $this->cm->getUrlContent(
                    $wsUrl.'/ws/opinions/countdirectoropinions/',
                    true
                );
                $opinions = $this->cm->getUrlContent(
                    $wsUrl.'/ws/opinions/allopinionsdirector/'.$page,
                    true
                );
            } else {
                // Regular authors
                $countOpinions = $this->cm->getUrlContent(
                    $wsUrl.'/ws/opinions/countauthoropinions/'.$author->id,
                    true
                );
                $opinions = $this->cm->getUrlContent(
                    $wsUrl.'/ws/opinions/allopinionsauthor/'.$page.'/'.$author->id,
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

            $pagination = $this->get('paginator')->get([
                'directional' => true,
                'epp'         => $itemsPerPage,
                'page'        => $page,
                'total'       => $countOpinions,
                'route'       => [
                    'name'   => 'frontend_opinion_external_author_frontpage',
                    'params' => [
                        'author_id'   => sprintf('%06d', $author->id),
                        'author_slug' => $author->slug,
                    ]
                ]
            ]);

            $this->view->assign(
                array(
                    'pagination' => $pagination,
                    'opinions'   => $opinions,
                    'author'     => $author,
                    'page'       => $page,
                    'ext'        => $externalMediaUrl,
                )
            );
        } // End if isCached

        list($positions, $advertisements) = $this->getAds();

        return $this->render('opinion/opinion_author_index.tpl', [
            'ads_positions'   => $positions,
            'advertisements'  => $advertisements,
            'cache_id'        => $cacheID,
            'actual_category' => 'opinion',
            'x-tags'          => 'ext-opinion-frontpage-author,page-'.$page.',author-'.$authorID,
            'x-cache-for'     => '+3 hours'
        ]);
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
        $dirtyID = $request->query->filter('opinion_id', '', FILTER_SANITIZE_STRING);
        $urlSlug = $request->query->filter('opinion_title', '', FILTER_SANITIZE_STRING);

        $opinion = $this->get('content_url_matcher')
            ->matchContentUrl('opinion', $dirtyID, $urlSlug);

        if (empty($opinion)) {
            throw new ResourceNotFoundException();
        }

        $subscriptionFilter = new \Frontend\Filter\SubscriptionFilter($this->view, $this->getUser());
        $cacheable = $subscriptionFilter->subscriptionHook($opinion);

        // Don't execute the app logic if there are caches available
        $cacheId = $this->view->generateCacheId($this->category_name, '', $opinion->id);
        if (($this->view->getCaching() === 0)
            || !$this->view->isCached('opinion/opinion.tpl', $cacheId)
        ) {
            $author = $this->get('user_repository')->find((int)$opinion->fk_author);
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
                            'blog_id'     => $dirtyID,
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
                $element = $this->get('opinion_repository')->find('Opinion', $suggest['pk_content']);
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
                $photo = $this->get('opinion_repository')->find('Photo', $opinion->img2);
                $this->view->assign('photo', $photo);
            }

            // Fetch the other opinions for this author
            $criteria = [];
            if ($opinion->type_opinion == 1) {
                $where =' opinions.type_opinion = 1';
                $opinion->name = 'Editorial';
                $opinion->author_name_slug = \Onm\StringUtils::getTitle($opinion->name);
                $this->view->assign('actual_category', 'editorial');
                $criteria = [ 'opinions`.`type_opinion' => [[ 'value' => 1]] ];
            } elseif ($opinion->type_opinion == 2) {
                $where =' opinions.type_opinion = 2';
                $opinion->name = 'Director';
                $opinion->author_name_slug = \Onm\StringUtils::getTitle($opinion->name);
                $criteria = [ 'opinions`.`type_opinion' => [[ 'value' => 2]] ];
            } else {
                $where =' opinions.fk_author='.($opinion->fk_author);
                $criteria = [ 'opinions`.`fk_author' => [[ 'value' => $opinion->fk_author ]] ];
            }

            $criteria['pk_opinion'] = [[ 'operator' => '<>', 'value' => $opinion->id ]];
            $criteria['content_status'] = [[ 'value' => 1 ]];
            $order = ['created' => 'desc'];

            $otherOpinions = $this->get('opinion_repository')->findBy($criteria, $order, 10, 1);

            foreach ($otherOpinions as &$otOpinion) {
                $otOpinion->author = $author;
                $otOpinion->author_name_slug  = $opinion->author_name_slug;
                $otOpinion->uri  = $otOpinion->uri;
            }

            $this->view->assign(
                [ 'other_opinions' => $otherOpinions, 'author' => $author]
            );
        } // End if isCached

        //Fetch information for Advertisements
        list($positions, $advertisements) = $this->getAds('inner');

        // Show in Frontpage
        return $this->render('opinion/opinion.tpl', [
            'ads_positions'   => $positions,
            'advertisements'  => $advertisements,
            'opinion'         => $opinion,
            'content'         => $opinion,
            'contentId'       => $opinion->id,
            'cache_id'        => $cacheId,
            'actual_category' => 'opinion',
            'x-tags'          => 'opinion,'.$opinion->id,
            'x-cache-for'     => '+1 day',
            'x-cacheable'     => $cacheable,
        ]);
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
                if (in_array($this->category_name, $values['categories'])) {
                    $wsUrl = $siteUrl;
                }
            }
        }

        $cacheID = $this->view->generateCacheId('sync'.$this->category_name, null, $dirtyID);

        if (($this->view->getCaching() === 0)
            || !$this->view->isCached('opinion/opinion.tpl', $cacheID)
        ) {
            $this->cm = new \ContentManager();

            $opinion = $this->cm->getUrlContent($wsUrl.'/ws/opinions/complete/'.$dirtyID, true);
            $opinion = unserialize($opinion);

            // Overload opinion object with category_name (used on ext_print)
            $opinion->category_name = $this->category_name;

            //Fetch information for Advertisements
            list($positions, $advertisements) = $this->getAds('inner');
            $this->view->assign('ads_positions', $positions);
            $this->view->assign('advertisements', $advertisements);

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
                        'contentId'       => $opinion->id,
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
                'x-tags'          => 'ext-opinion,'.$opinion->id,
                'x-cache-for'     => '+1 day',
            )
        );
    }

    /**
     * Fetches the advertisement
     *
     * @param string $context the context to fetch ads from
     *
     * TODO: Make this function non-static
     */
    public static function getAds($context = '')
    {
        // TODO: Use $this->get when the function changes to non-static
        $positionManager = getService('core.manager.advertisement');
        if ($context == 'inner') {
            $positions = $positionManager->getPositionsForGroup('opinion_inner', array(7, 9));
        } else {
            $positions = $positionManager->getPositionsForGroup('opinion_frontpage', array(7, 9));
        }

        $advertisements = \Advertisement::findForPositionIdsAndCategory($positions, '4');

        return [ $positions, $advertisements ];
    }
}
