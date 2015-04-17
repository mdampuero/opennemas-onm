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
use Backend\Annotation\CheckModuleAccess;
use Onm\Security\Acl;
use Onm\Module\ModuleManager;
use Onm\Framework\Controller\Controller;
use Onm\Settings as s;

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
     * @param  $blog      Blog flag for listing
     * @return Response   The response object.
     *
     * @Security("has_role('OPINION_ADMIN')")
     *
     * @CheckModuleAccess(module="OPINION_MANAGER")
     */
    public function listAction($blog)
    {
        // Fetch all authors
        $allAuthors = \User::getAllUsersAuthors();

        $authors = [
            [ 'name' => _('All'), 'value' => -1 ],
        ];

        foreach ($allAuthors as $author) {
            $authors[] = [ 'name' => $author->name, 'value' => $author->id ];
        }

        return $this->render(
            'opinion/list.tpl',
            array(
                'authors' => $authors,
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
     *
     * @CheckModuleAccess(module="OPINION_MANAGER")
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
            'ORDER BY position ASC , created DESC LIMIT ' . $numOpinions
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
            $this->get('session')->getFlashBag()->add(
                'notice',
                sprintf(_("You must put %d opinions %s in the frontpage "), $numOpinions, 'opinions')
            );
        }

        if (($numEditorial > 0) && (count($editorial) != $numEditorial)) {
            $this->get('session')->getFlashBag()->add(
                'notice',
                sprintf(_("You must put %d opinions %s in the frontpage "), $numEditorial, 'editorial')
            );
        }
        if (($numDirector>0) && (count($director) != $numDirector)) {
            $this->get('session')->getFlashBag()->add(
                'notice',
                sprintf(_("You must put %d opinions %s in the frontpage "), $numDirector, 'opinion del director')
            );
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
     *
     * @CheckModuleAccess(module="OPINION_MANAGER")
     */
    public function showAction(Request $request)
    {
        $id = $request->query->getDigits('id', null);

        $opinion = new \Opinion($id);

        // Check if opinion id exists
        if (is_null($opinion->id)) {
            $this->get('session')->getFlashBag()->add(
                'error',
                sprintf(_('Unable to find the opinion with the id "%d"'), $id)
            );

            return $this->redirect($this->generateUrl('admin_opinions'));
        }

        // Check if you can see others opinions
        if (!Acl::isAdmin()
            && !Acl::check('CONTENT_OTHER_UPDATE')
            && $opinion->fk_author != $_SESSION['userid']
        ) {
            $this->get('session')->getFlashBag()->add(
                'error',
                _("You can't modify this opinion because you don't have enought privileges.")
            );

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
                'opinion'        => $opinion,
                'all_authors'    => $allAuthors,
                'author'         => $author,
                'commentsConfig' => s::get('comments_config'),
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
     *
     * @CheckModuleAccess(module="OPINION_MANAGER")
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
                $this->get('session')->getFlashBag()->add(
                    'success',
                    _('Opinion successfully created.')
                );

                // Clear caches
                dispatchEventWithParams('opinion.create', array('authorId' => $data['fk_author']));
            } else {
                $this->get('session')->getFlashBag()->add(
                    'error',
                    _('Unable to create the new opinion.')
                );
            }

            return $this->redirect(
                $this->generateUrl('admin_opinion_show', array('id' => $opinion->id))
            );
        } else {
            // Fetch all authors
            $allAuthors = \User::getAllUsersAuthors();

            return $this->render(
                'opinion/new.tpl',
                array(
                    'all_authors'    => $allAuthors,
                    'commentsConfig' => s::get('comments_config'),
                )
            );
        }
    }

    /**
     * Updates the opinion information sent by POST.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     *
     * @Security("has_role('OPINION_UPDATE')")
     *
     * @CheckModuleAccess(module="OPINION_MANAGER")
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
                $this->get('session')->getFlashBag()->add(
                    'error',
                    _("You can't modify this opinion because you don't have enought privileges.")
                );

                return $this->redirect($this->generateUrl('admin_opinions'));
            }

            $contentStatus = $request->request->filter('content_status', '', FILTER_SANITIZE_STRING);
            $inhome      = $request->request->filter('in_home', '', FILTER_SANITIZE_STRING);
            $withComment = $request->request->filter('with_comment', '', FILTER_SANITIZE_STRING);

            // Check empty data
            if (count($request->request) < 1) {
                $this->get('session')->getFlashBag()->add(
                    'error',
                    _("Opinion data sent not valid.")
                );

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
                $this->get('session')->getFlashBag()->add(
                    'success',
                    _('Opinion successfully updated.')
                );

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
                $this->get('session')->getFlashBag()->add(
                    'error',
                    _('Unable to update the opinion.')
                );
            }

            return $this->redirect(
                $this->generateUrl('admin_opinion_show', array('id' => $opinion->id))
            );
        }

    }

    /**
     * Change in_home status for one opinion given its id.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     *
     * @Security("has_role('OPINION_HOME')")
     *
     * @CheckModuleAccess(module="OPINION_MANAGER")
     */
    public function toggleInHomeAction(Request $request)
    {
        $id     = $request->query->getDigits('id', 0);
        $status = $request->query->getDigits('status', 0);
        $type   = $request->query->filter('type', 0, FILTER_SANITIZE_STRING);
        $page   = $request->query->getDigits('page', 1);

        $opinion = new \Opinion($id);

        if (is_null($opinion->id)) {
            $this->get('session')->getFlashBag()->add(
                'error',
                sprintf(_('Unable to find an opinion with the id "%d"'), $id)
            );
        } else {
            $opinion->setInHome($status, $_SESSION['userid']);

            $this->get('session')->getFlashBag()->add(
                'success',
                sprintf(_('Successfully changed in home state for the opinion "%s"'), $opinion->title)
            );
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
     * Lists the available opinions for the frontpage manager.
     *
     * @param  Request $request The request object.
     * @return Response         The response object.
     *
     * @CheckModuleAccess(module="OPINION_MANAGER")
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

        $pagination = $this->get('paginator')->create([
            'spacesBeforeSeparator' => 0,
            'spacesAfterSeparator'  => 0,
            'firstLinkTitle'        => '',
            'lastLinkTitle'         => '',
            'separator'             => '',
            'firstPagePre'          => '',
            'firstPageText'         => '',
            'firstPagePost'         => '',
            'lastPagePre'           => '',
            'lastPageText'          => '',
            'lastPagePost'          => '',
            'prevImg'               => _('Previous'),
            'nextImg'               => _('Next'),
            'elements_per_page'     => $itemsPerPage,
            'total_items'           => $countOpinions,
            'delta'                 => 1,
            'base_url'              => $this->generateUrl(
                'admin_opinions_content_provider',
                array('category' => $categoryId)
            ),
        ]);

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
     *
     * @CheckModuleAccess(module="OPINION_MANAGER")
     */
    public function contentProviderRelatedAction(Request $request)
    {
        $page         = $request->query->getDigits('page', 1);
        $itemsPerPage = s::get('items_per_page') ?: 20;

        $filters = array(
            'content_type_name' => array(array('value' => 'opinion')),
            'in_litter'         => array(array('value' => 1, 'operator' => '!='))
        );

        $em            = $this->get('entity_repository');
        $opinions      = $em->findBy($filters, array('created' => 'desc'), $itemsPerPage, $page);
        $countOpinions = $em->countBy($filters);

        $pagination = $this->get('paginator')->create([
            'elements_per_page' => $itemsPerPage,
            'total_items'       => $countOpinions,
            'delta'             => 1,
            'base_url'          => $this->generateUrl('admin_opinions_content_provider_related'),
        ]);

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
     *
     * @CheckModuleAccess(module="OPINION_MANAGER")
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
                    'blog_orderFrontpage'   => filter_var($configsRAW['blog_orderFrontpage'], FILTER_SANITIZE_STRING),
                    'blog_itemsFrontpage'   => filter_var($configsRAW['blog_itemsFrontpage'], FILTER_VALIDATE_INT),
                )
            );

            foreach ($configs as $key => $value) {
                s::set($key, $value);
            }

            $this->get('session')->getFlashBag()->add(
                'success',
                _('Settings saved successfully.')
            );

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
     * Previews an opinion in frontend by sending the opinion info by POST.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     *
     * @Security("has_role('OPINION_ADMIN')")
     *
     * @CheckModuleAccess(module="OPINION_MANAGER")
     */
    public function previewAction(Request $request)
    {
        $opinion = new \Opinion();
        $cm = new  \ContentManager();
        $this->view = new \Template(TEMPLATE_USER);

        $opinionContents = $request->request->filter('contents');

        // Fetch all opinion properties and generate a new object
        foreach ($opinionContents as $value) {
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
        $opinion->author_name_slug = \Onm\StringUtils::getTitle($opinion->name);

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
                $suggest['author_name_slug'] = \Onm\StringUtils::getTitle($element->author);
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
            $where.' AND `pk_opinion` <>' .$opinion->id.' AND content_status=1',
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
     * @return Response  The response object.
     *
     * @Security("has_role('OPINION_ADMIN')")
     *
     * @CheckModuleAccess(module="OPINION_MANAGER")
     */
    public function getPreviewAction()
    {
        $session = $this->get('session');

        $content = $session->get('last_preview');
        $session->remove('last_preview');

        return new Response($content);
    }
}
