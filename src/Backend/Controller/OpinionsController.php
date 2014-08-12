<?php
/**
 * Handles the actions for managing opinions
 *
 * @package Backend_Controllers
 **/
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 **/
namespace Backend\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Onm\Security\Acl;
use Onm\Framework\Controller\Controller;
use Onm\Message as m;
use Onm\Settings as s;
use \Onm\Module\ModuleManager;

/**
 * Handles the actions for managing opinions
 *
 * @package Backend_Controllers
 **/
class OpinionsController extends Controller
{
    /**
     * Common code for all the actions
     */
    public function init()
    {
        //Check if module is activated in this onm instance
        ModuleManager::checkActivatedOrForward('OPINION_MANAGER');

        $this->ccm  = \ContentCategoryManager::get_instance();

        list($this->parentCategories, $this->subcat, $this->categoryData)
            = $this->ccm->getArraysMenu();

        $timezones = \DateTimeZone::listIdentifiers();
        $timezone  = new \DateTimeZone($timezones[s::get('time_zone', 'UTC')]);

        $this->view->assign(
            array(
                'allcategorys' => $this->parentCategories,
                'timezone'     => $timezone->getName()
            )
        );
    }

    /**
     * Lists all the opinions.
     *
     * @param  Request $request The request object.
     * @return Response         The response object.
     *
     * @Security("has_role('OPINION_ADMIN')")
     */
    public function listAction(Request $request, $blog)
    {
        // Fetch all authors
        $allAuthors = \User::getAllUsersAuthors();

        return $this->render(
            'opinion/list.tpl',
            array(
                'autores' => $allAuthors,
                'blog'    => $blog,
                'home'    => false,
            )
        );
    }

    /**
     * Manages the frontpage of opinion.
     *
     * @param  Request $request The request object.
     * @return Response         The response object.
     *
     * @Security("has_role('OPINION_FRONTPAGE')")
     */
    public function frontpageAction(Request $request)
    {
        $page =  $request->query->getDigits('page', 1);
        $configurations = s::get('opinion_settings');

        $numEditorial = $configurations['total_editorial'];
        $numDirector  = $configurations['total_director'];
        $numOpinions  = s::get('items_per_page');
        if (!empty($configurations) && array_key_exists('total_opinions', $configurations)) {
            $numOpinions = $configurations['total_opinions'];
        }

        $cm = new \ContentManager();
        $allAuthors = \User::getAllUsersAuthors();

        $authorsBlog = array();
        foreach ($allAuthors as $authorData) {
            if ($authorData->is_blog == 1) {
                $authorsBlog[$authorData->id] = $authorData;
            }
        }
        $where ='';
        if (!empty($authorsBlog)) {
            $where .= ' AND opinions.fk_author NOT IN ('.implode(', ', array_keys($authorsBlog)).") ";
        }

        $opinions = $cm->find(
            'Opinion',
            'in_home=1 and content_status=1 and type_opinion=0 '.$where,
            'ORDER BY position ASC , created DESC'
        );

        $editorial = array();
        if ($numEditorial > 0) {
            $editorial = $cm->find(
                'Opinion',
                'in_home=1 and content_status=1 and type_opinion=1',
                'ORDER BY position ASC, created DESC LIMIT '.$numEditorial
            );
        }
        $director = array();
        if ($numDirector >0) {
            $director = $cm->find(
                'Opinion',
                'in_home=1 and content_status=1 and type_opinion=2',
                'ORDER BY position ASC , created DESC LIMIT '.$numDirector
            );
        }

        if (($numOpinions > 0) && (count($opinions) > $numOpinions)) {
            m::add(sprintf(count($opinions) . _("You must put %d opinions %s in the frontpage "), $numOpinions, 'opinions'));
        }

        if (($numEditorial > 0) && (count($editorial) != $numEditorial)) {
            m::add(sprintf(_("You must put %d opinions %s in the frontpage"), $numEditorial, 'editorial'));
        }
        if (($numDirector>0) && (count($director) != $numDirector)) {
             m::add(sprintf(_("You must put %d opinions %s in the frontpage"), $numDirector, 'opinion del director'));
        }

        if (isset($opinions) && is_array($opinions)) {
            foreach ($opinions as &$opinion) {
                $opinion->author = new \User($opinion->fk_author);
            }
        } else {
            $opinions = array();
        }

        // Fetch all authors
        $allAuthors = \User::getAllUsersAuthors();

        return $this->render(
            'opinion/list.tpl',
            array(
                'autores'    => $allAuthors,
                'opinions'   => $opinions,
                'director'   => $director,
                'editorial'  => $editorial,
                'type'       => 'frontpage',
                'page'       => $page,
                'home'       => true,
            )
        );
    }

    /**
     * Shows the information form for a opinion given its id.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     *
     * @Security("has_role('OPINION_UPDATE')")
     */
    public function showAction(Request $request)
    {
        $id = $request->query->getDigits('id', null);

        $opinion = new \Opinion($id);

        // Check if opinion id exists
        if (is_null($opinion->id)) {
            m::add(sprintf(_('Unable to find the opinion with the id "%d"'), $id));

            return $this->redirect($this->generateUrl('admin_opinions'));
        }

        // Check if you can see others opinions
        if (!Acl::isAdmin()
            && !Acl::check('CONTENT_OTHER_UPDATE')
            && $opinion->fk_author != $_SESSION['userid']
        ) {
            m::add(_("You can't modify this opinion because you don't have enought privileges."));

            return $this->redirect($this->generateUrl('admin_opinions'));
        }

        // Fetch author data and allAuthors
        $author = new \User($opinion->fk_author);
        $allAuthors = \User::getAllUsersAuthors();

        // Fetch associated photos with opinion
        if (!empty($opinion->image)) {
            $image = new \Photo($opinion->image);
            $this->view->assign('image', $image);
        }

        if (!empty($opinion->img1)) {
            $photo1 = new \Photo($opinion->img1);
            $this->view->assign('photo1', $photo1);
        }

        if (!empty($opinion->img2)) {
            $photo2 = new \Photo($opinion->img2);
            $this->view->assign('photo2', $photo2);
        }

        return $this->render(
            'opinion/new.tpl',
            array(
                'opinion'      => $opinion,
                'all_authors'  => $allAuthors,
                'author'       => $author,
            )
        );
    }

    /**
     * Handles the form for creating a new opinion.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     *
     * @Security("has_role('OPINION_CREATE')")
     */
    public function createAction(Request $request)
    {
        if ('POST' == $request->getMethod()) {
            $opinion = new \Opinion();

            $contentStatus = $request->request->filter('content_status', '', FILTER_SANITIZE_STRING);
            $inhome        = $request->request->filter('in_home', '', FILTER_SANITIZE_STRING);
            $withComment   = $request->request->filter('with_comment', '', FILTER_SANITIZE_STRING);

            $data = array(
                'title'               => $request->request->filter('title', '', FILTER_SANITIZE_STRING),
                'category'            => 'opinion',
                'content_status'      => (empty($contentStatus)) ? 0 : 1,
                'in_home'             => (empty($inhome)) ? 0 : 1,
                'with_comment'        => (empty($withComment)) ? 0 : 1,
                'summary'             => $request->request->filter('summary', '', FILTER_SANITIZE_STRING),
                'img1'                => $request->request->filter('img1', '', FILTER_SANITIZE_STRING),
                'img1_footer'         => $request->request->filter('img1_footer', '', FILTER_SANITIZE_STRING),
                'img2'                => $request->request->filter('img2', '', FILTER_SANITIZE_STRING),
                'img2_footer'         => $request->request->filter('img2_footer', '', FILTER_SANITIZE_STRING),
                'type_opinion'        => $request->request->filter('type_opinion', '', FILTER_SANITIZE_STRING),
                'fk_author'           => $request->request->getDigits('fk_author'),
                'fk_user_last_editor' => $request->request->getDigits('fk_user_last_editor'),
                'metadata'            => $request->request->filter('metadata', '', FILTER_SANITIZE_STRING),
                'body'                => $request->request->filter('body', '', FILTER_SANITIZE_STRING),
                'fk_author_img'       => $request->request->getDigits('fk_author_img'),
                'fk_publisher'        => $_SESSION['userid'],
                'starttime'           => $request->request->filter('starttime', '', FILTER_SANITIZE_STRING),
                'endtime'             => $request->request->filter('endtime', '', FILTER_SANITIZE_STRING),
            );

            if ($opinion->create($data)) {
                m::add(_('Opinion successfully created.'), m::SUCCESS);

                // Clear caches
                dispatchEventWithParams('opinion.create', array('authorId' => $data['fk_author']));
            } else {
                m::add(_('Unable to create the new opinion.'), m::ERROR);
            }

            $continue = $request->request->filter('continue', false, FILTER_SANITIZE_STRING);
            if (isset($continue) && $continue==1) {
                return $this->redirect(
                    $this->generateUrl('admin_opinion_show', array('id' => $opinion->id))
                );
            } else {
                return $this->redirect(
                    $this->generateUrl('admin_opinions', array('type_opinion' => $data['category']))
                );
            }
        } else {
            // Fetch all authors
            $allAuthors = \User::getAllUsersAuthors();

            return $this->render('opinion/new.tpl', array('all_authors' => $allAuthors));
        }
    }

    /**
     * Updates the opinion information sent by POST.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     *
     * @Security("has_role('OPINION_UPDATE')")
     */
    public function updateAction(Request $request)
    {
        $id = $request->query->getDigits('id');

        $opinion = new \Opinion($id);

        if ($opinion->id != null) {
            if (!Acl::isAdmin()
                && !Acl::check('CONTENT_OTHER_UPDATE')
                && !$opinion->isOwner($_SESSION['userid'])
            ) {
                m::add(_("You can't modify this opinion because you don't have enought privileges."));

                return $this->redirect($this->generateUrl('admin_opinions'));
            }

            $contentStatus = $request->request->filter('content_status', '', FILTER_SANITIZE_STRING);
            $inhome      = $request->request->filter('in_home', '', FILTER_SANITIZE_STRING);
            $withComment = $request->request->filter('with_comment', '', FILTER_SANITIZE_STRING);

            // Check empty data
            if (count($request->request) < 1) {
                m::add(_("Opinion data sent not valid."), m::ERROR);

                return $this->redirect($this->generateUrl('admin_opinion_show', array('id' => $id)));
            }

            $data = array(
                'id'                  => $id,
                'title'               => $request->request->filter('title', '', FILTER_SANITIZE_STRING),
                'category'            => 'opinion',
                'content_status'      => (empty($contentStatus)) ? 0 : 1,
                'in_home'             => (empty($inhome)) ? 0 : 1,
                'with_comment'        => (empty($withComment)) ? 0 : 1,
                'summary'             => $request->request->filter('summary', '', FILTER_SANITIZE_STRING),
                'img1'                => $request->request->filter('img1', '', FILTER_SANITIZE_STRING),
                'img1_footer'         => $request->request->filter('img1_footer', '', FILTER_SANITIZE_STRING),
                'img2'                => $request->request->filter('img2', '', FILTER_SANITIZE_STRING),
                'img2_footer'         => $request->request->filter('img2_footer', '', FILTER_SANITIZE_STRING),
                'type_opinion'        => $request->request->filter('type_opinion', '', FILTER_SANITIZE_STRING),
                'fk_author'           => $request->request->getDigits('fk_author'),
                'fk_user_last_editor' => $request->request->getDigits('fk_user_last_editor'),
                'metadata'            => $request->request->filter('metadata', '', FILTER_SANITIZE_STRING),
                'body'                => $request->request->filter('body', '', FILTER_SANITIZE_STRING),
                'fk_author_img'       => $request->request->getDigits('fk_author_img'),
                'fk_publisher'        => $_SESSION['userid'],
                'starttime'           => $request->request->filter('starttime', '', FILTER_SANITIZE_STRING),
                'endtime'             => $request->request->filter('endtime', '', FILTER_SANITIZE_STRING),
            );

            if ($opinion->update($data)) {
                m::add(_('Opinion successfully updated.'), m::SUCCESS);

                $author = new \User($data['fk_author']);

                // Clear caches
                dispatchEventWithParams(
                    'opinion.update',
                    array(
                        'authorSlug' => $author->username,
                        'authorId'   => $data['fk_author'],
                        'opinionId'  => $opinion->id,
                    )
                );
            } else {
                m::add(_('Unable to update the opinion.'), m::ERROR);
            }

            $continue = $request->request->filter('continue', false, FILTER_SANITIZE_STRING);
            if (isset($continue) && $continue==1) {
                return $this->redirect(
                    $this->generateUrl('admin_opinion_show', array('id' => $opinion->id))
                );
            } else {
                return $this->redirect(
                    $this->generateUrl('admin_opinions', array('type_opinion' => $data['category']))
                );
            }
        }

    }

    /**
     * Change in_home status for one opinion given its id.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     *
     * @Security("has_role('OPINION_HOME')")
     */
    public function toggleInHomeAction(Request $request)
    {
        $id     = $request->query->getDigits('id', 0);
        $status = $request->query->getDigits('status', 0);
        $type   = $request->query->filter('type', 0, FILTER_SANITIZE_STRING);
        $page   = $request->query->getDigits('page', 1);

        $opinion = new \Opinion($id);

        if (is_null($opinion->id)) {
            m::add(sprintf(_('Unable to find an opinion with the id "%d"'), $id), m::ERROR);
        } else {
            $opinion->set_inhome($status, $_SESSION['userid']);
            m::add(sprintf(_('Successfully changed in home state for the opinion "%s"'), $opinion->title), m::SUCCESS);
        }

        if ($type != 'frontpage') {
            $url = $this->generateUrl(
                'admin_opinions',
                array('type' => $type, 'page' => $page)
            );
        } else {
            $url = $this->generateUrl(
                'admin_opinions_frontpage',
                array('page' => $page)
            );
        }

         return $this->redirect($url);
    }

    /**
     * Saves the widget opinions content positions.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     *
     * @Security("has_role('OPINION_ADMIN')")
     */
    public function savePositionsAction(Request $request)
    {
        $containers = json_decode($request->get('positions'));

        $result = true;

        if (isset($containers)
            && is_array($containers)
            && count($containers) > 0
        ) {
            foreach ($containers as $elements) {
                $pos = 1;
                foreach ($elements as $id) {
                    $opinion = new \Opinion($id);
                    $result = $result &&  $opinion->setPosition($pos);

                    $pos++;
                }
            }
        }

        dispatchEventWithParams('frontpage.save_position', array('category' => 'opinion'));

        if ($result === true) {
            $message = _('Positions saved successfully.');
            $output = sprintf(
                '<div class="alert alert-success">%s<button data-dismiss="alert" class="close">×</button></div>',
                $message
            );
        } else {
            $message = _('Unable to save the positions.');
            $output = sprintf(
                '<div class="alert alert-error">%s<button data-dismiss="alert" class="close">×</button></div>',
                $message
            );
        }

        return new Response($output);
    }

    /**
     * Lists the available opinions for the frontpage manager.
     *
     * @param  Request $request The request object.
     * @return Response         The response object.
     */
    public function contentProviderAction(Request $request)
    {
        $categoryId   = $request->query->getDigits('category', 0);
        $page         = $request->query->getDigits('page', 1);
        $itemsPerPage = 8;

        $em  = $this->get('entity_repository');
        $ids = $this->get('frontpage_repository')->getContentIdsForHomepageOfCategory();

        $filters = array(
            'content_type_name' => array(array('value' => 'opinion')),
            'content_status'    => array(array('value' => 1)),
            'in_litter'         => array(array('value' => 1, 'operator' => '!=')),
            'pk_content'        => array(array('value' => $ids, 'operator' => 'NOT IN'))
        );

        $opinions      = $em->findBy($filters, array('created' => 'desc'), $itemsPerPage, $page);
        $countOpinions = $em->countBy($filters);

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
                    'admin_opinions_content_provider',
                    array('category' => $categoryId)
                ).'&page=%d',
            )
        );

        return $this->render(
            'opinion/content-provider.tpl',
            array(
                'opinions' => $opinions,
                'pager'    => $pagination,
            )
        );
    }

    /**
     * Lists the latest opinions for the related manager.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     */
    public function contentProviderRelatedAction(Request $request)
    {
        $categoryId   = $request->query->getDigits('category', 0);
        $page         = $request->query->getDigits('page', 1);
        $itemsPerPage = s::get('items_per_page') ?: 20;

        $em       = $this->get('entity_repository');
        $category = $this->get('category_repository')->find($categoryId);

        $filters = array(
            'content_type_name' => array(array('value' => 'opinion')),
            'in_litter'         => array(array('value' => 1, 'operator' => '!='))
        );

        $opinions      = $em->findBy($filters, array('created' => 'desc'), $itemsPerPage, $page);
        $countOpinions = $em->countBy($filters);

        $pagination = \Pager::factory(
            array(
                'mode'        => 'Sliding',
                'perPage'     => $itemsPerPage,
                'append'      => false,
                'path'        => '',
                'delta'       => 1,
                'clearIfVoid' => true,
                'urlVar'      => 'page',
                'totalItems'  => $countOpinions,
                'fileName'    => $this->generateUrl('admin_opinions_content_provider_related').'?page=%d',
            )
        );

        return $this->render(
            'common/content_provider/_container-content-list.tpl',
            array(
                'contentType'           => 'Opinion',
                'contents'              => $opinions,
                'pagination'            => $pagination->links,
                'contentProviderUrl'    => $this->generateUrl('admin_opinions_content_provider_related'),
            )
        );
    }

    /**
     * Handles the configuration for the opinion manager.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     *
     * @Security("has_role('OPINION_SETTINGS')")
     */
    public function configAction(Request $request)
    {
        if ('POST' == $request->getMethod()) {

            $configsRAW = $request->request->get('opinion_settings');

            $configs = array(
                'opinion_settings' => array(
                    'total_director'        => filter_var($configsRAW['total_director'], FILTER_VALIDATE_INT),
                    'total_editorial'       => filter_var($configsRAW['total_editorial'], FILTER_VALIDATE_INT),
                    'total_opinions'        => filter_var($configsRAW['total_opinions'], FILTER_VALIDATE_INT),
                    'total_opinion_authors' => filter_var($configsRAW['total_opinion_authors'], FILTER_VALIDATE_INT),
                )
            );

            foreach ($configs as $key => $value) {
                s::set($key, $value);
            }

            m::add(_('Settings saved successfully.'), m::SUCCESS);

            return $this->redirect($this->generateUrl('admin_opinions_config'));
        } else {
            $configurations = s::get(array('opinion_settings'));

            return $this->render(
                'opinion/config.tpl',
                array('configs'   => $configurations)
            );
        }
    }

    /**
     * Show a list of opinion authors.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     *
     * @Security("has_role('AUTHOR_ADMIN')")
     */
    public function listAuthorAction(Request $request)
    {
        return $this->render('opinion/author_list.tpl');
    }

    /**
     * Shows the author information given its id.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     *
     * @Security("has_role('AUTHOR_UPDATE')")
     */
    public function showAuthorAction(Request $request)
    {
        $id = $request->query->getDigits('id');

        $user = new \User($id);
        if (is_null($user->id)) {
            m::add(sprintf(_("Unable to find the author with the id '%d'"), $id), m::ERROR);

            return $this->redirect($this->generateUrl('admin_opinion_authors'));
        }

        // Fetch user photo if exists
        if (!empty($user->avatar_img_id)) {
            $user->photo = new \Photo($user->avatar_img_id);
        }

        $user->meta = $user->getMeta();
        if (array_key_exists('is_blog', $user->meta)) {
            $user->is_blog = $user->meta['is_blog'];
        } else {
            $user->is_blog = 0;
        }

        return $this->render('opinion/author_new.tpl', array('user' => $user));
    }

    /**
     * Creates an author give some information.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     *
     * @Security("has_role('AUTHOR_CREATE')")
     */
    public function createAuthorAction(Request $request)
    {
        $user = new \User();

        if ($request->getMethod() == 'POST') {
            $data = array(
                'email'           => $request->request->filter('email', null, FILTER_SANITIZE_STRING),
                'name'            => $request->request->filter('name', null, FILTER_SANITIZE_STRING),
                'sessionexpire'   => 60,
                'bio'             => $request->request->filter('bio', '', FILTER_SANITIZE_STRING),
                'url'             => $request->request->filter('url', '', FILTER_SANITIZE_STRING),
                'id_user_group'   => array(3),
                'meta[is_blog]'   => $request->request->filter('meta[is_blog]', '', FILTER_SANITIZE_STRING),
                'ids_category'    => array(),
                'activated'       => 0,
                'type'            => 0,
                'deposit'         => 0,
                'token'           => null,
            );

            // Generate username and password from real name
            $data['username'] = strtolower(str_replace('-', '.', \Onm\StringUtils::get_title($data['name'])));
            $data['password'] = md5($data['name']);

            $file = $request->files->get('avatar');

            try {
                // Upload user avatar if exists
                if (!is_null($file)) {
                    $photoId = $user->uploadUserAvatar($file, \Onm\StringUtils::get_title($data['name']));
                    $data['avatar_img_id'] = $photoId;
                } else {
                    $data['avatar_img_id'] = 0;
                }

                if ($user->create($data)) {
                    // Set all usermeta information (twitter, rss, language)
                    $meta = $request->request->get('meta');
                    $meta['is_blog'] = (empty($meta['is_blog'])) ? 0 : 1;
                    foreach ($meta as $key => $value) {
                        $user->setMeta(array($key => $value));
                    }

                    m::add(_('Author created successfully.'), m::SUCCESS);

                    return $this->redirect(
                        $this->generateUrl(
                            'admin_opinion_author_show',
                            array('id' => $user->id)
                        )
                    );
                } else {
                    m::add(_('Unable to create the author with that information'), m::ERROR);
                }
            } catch (\Exception $e) {
                m::add($e->getMessage(), m::ERROR);
            }
        }

        return $this->render('opinion/author_new.tpl', array('user' => $user));
    }

    /**
     * Handles the update action for an author given its id.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     *
     * @Security("has_role('AUTHOR_UPDATE')")
     */
    public function updateAuthorAction(Request $request)
    {
        $userId = $request->query->getDigits('id');

        if (count($request->request) < 1) {
            m::add(_("User data sent not valid."), m::ERROR);

            return $this->redirect(
                $this->generateUrl('admin_opinion_author_show', array('id' => $userId))
            );
        }

        $user   = new \User($userId);

        $accessCategories = array();
        foreach ($user->accesscategories as $key => $value) {
            $accessCategories[] = (int)$value->pk_content_category;
        }

        $data = array(
            'id'              => $userId,
            'email'           => $request->request->filter('email', null, FILTER_SANITIZE_STRING),
            'name'            => $request->request->filter('name', null, FILTER_SANITIZE_STRING),
            'bio'             => $request->request->filter('bio', '', FILTER_SANITIZE_STRING),
            'url'             => $request->request->filter('url', '', FILTER_SANITIZE_STRING),
            'type'            => $request->request->filter('type', '0', FILTER_SANITIZE_STRING),
            'meta[is_blog]'   => $request->request->filter('meta[is_blog]', '0', FILTER_SANITIZE_STRING),
            'sessionexpire'   => 60,
            'id_user_group'   => $user->id_user_group,
            'ids_category'    => $accessCategories,
            'avatar_img_id'   => $request->request->filter('avatar', null, FILTER_SANITIZE_STRING),
            'username'        => $request->request->filter('username', null, FILTER_SANITIZE_STRING),
        );

        $file = $request->files->get('avatar');

        // Generate username and password from real name
        if (empty($data['username'])) {
            $data['username'] = strtolower(str_replace('-', '.', \Onm\StringUtils::get_title($data['name'])));
        }

        try {
            // Upload user avatar if exists
            if (!is_null($file)) {
                $photoId = $user->uploadUserAvatar($file, \Onm\StringUtils::get_title($data['name']));
                $data['avatar_img_id'] = $photoId;
            } elseif (($data['avatar_img_id']) == 1) {
                $data['avatar_img_id'] = $user->avatar_img_id;
            }

            // Process data
            if ($user->update($data)) {
                // Set all usermeta information (twitter, rss, language)
                $meta = $request->request->get('meta');
                $meta['is_blog'] = (empty($meta['is_blog'])) ? 0 : 1;
                foreach ($meta as $key => $value) {
                    $user->setMeta(array($key => $value));
                }

                // Clear caches
                dispatchEventWithParams('author.update', array('id' => $userId));

                m::add(_('Author data updated successfully.'), m::SUCCESS);
            } else {
                m::add(_('Unable to update the author with that information'), m::ERROR);
            }
        } catch (\Exception $e) {
            m::add($e->getMessage(), m::ERROR);
        }

        return $this->redirect(
            $this->generateUrl('admin_opinion_author_show', array('id' => $userId))
        );
    }

    /**
     * Previews an opinion in frontend by sending the opinion info by POST.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     *
     * @Security("has_role('OPINION_ADMIN')")
     */
    public function previewAction(Request $request)
    {
        $opinion = new \Opinion();
        $cm = new  \ContentManager();
        $this->view = new \Template(TEMPLATE_USER);

        $opinionContents = $request->request->filter('contents');

        // Fetch all opinion properties and generate a new object
        foreach ($opinionContents as $key => $value) {
            if (isset($value['name']) && !empty($value['name'])) {
                $opinion->$value['name'] = $value['value'];
            }
        }

        // Set a dummy Id for the opinion if doesn't exists
        if (empty($opinion->pk_article) && empty($opinion->id)) {
            $opinion->pk_article = '-1';
            $opinion->id = '-1';
        }

        //Fetch information for Advertisements
        \Frontend\Controller\OpinionsController::getAds('inner');

        $author = new \User($opinion->fk_author);
        $opinion->author = $author;

        // Rescato esta asignación para que genere correctamente el enlace a frontpage de opinion
        $opinion->author_name_slug = \Onm\StringUtils::get_title($opinion->name);

        // Machine suggested contents code -----------------------------
        $machineSuggestedContents = $this->get('automatic_contents')->searchSuggestedContents(
            'opinion',
            " pk_content <>".$opinion->id,
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

        // Associated media code --------------------------------------
        $photo = '';
        if (isset($opinion->img2) && ($opinion->img2 > 0)) {
            $photo = new \Photo($opinion->img2);
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

        $otherOpinions = $cm->find(
            'Opinion',
            $where.' AND `pk_opinion` <>' .$opinionID.' AND content_status=1',
            ' ORDER BY created DESC LIMIT 0,9'
        );

        foreach ($otherOpinions as &$otOpinion) {
            $otOpinion->author           = $author;
            $otOpinion->author_name_slug = $opinion->author_name_slug;
            $otOpinion->uri              = $otOpinion->uri;
        }

        $this->view->caching = 0;

        $session = $this->get('session');

        $session->set(
            'last_preview',
            $this->view->fetch(
                'opinion/opinion.tpl',
                array(
                    'opinion'        => $opinion,
                    'content'        => $opinion,
                    'other_opinions' => $otherOpinions,
                    'author'         => $author,
                    'contentId'      => $opinion->id,
                    'photo'          => $photo,
                    'suggested'      => $machineSuggestedContents
                )
            )
        );

        return new Response('OK');
    }

    /**
     * Description of this action.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     *
     * @Security("has_role('OPINION_ADMIN')")
     */
    public function getPreviewAction(Request $request)
    {
        $session = $this->get('session');

        $content = $session->get('last_preview');
        $session->remove('last_preview');

        return new Response($content);
    }
}
