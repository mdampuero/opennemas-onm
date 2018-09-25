<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Backend\Controller;

use Common\Core\Annotation\Security;
use Common\Core\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class OpinionsController extends Controller
{
    /**
     * The name of the setting to save extra field configuration.
     *
     * @var string
     */
    const EXTRA_INFO_TYPE = 'extraInfoContents.OPINION_MANAGER';

    /**
     * Lists all the opinions.
     *
     * @return Response The response object.
     *
     * @Security("hasExtension('OPINION_MANAGER')
     *     and hasPermission('OPINION_ADMIN')")
     */
    public function listAction()
    {
        return $this->render('opinion/list.tpl', [ 'home' => false ]);
    }

    /**
     * Manages the frontpage of opinion.
     *
     * @param  Request $request The request object.
     * @return Response         The response object.
     *
     * @Security("hasExtension('OPINION_MANAGER')
     *     and hasPermission('OPINION_FRONTPAGE')")
     */
    public function frontpageAction(Request $request)
    {
        $page   = $request->query->getDigits('page', 1);
        $config = $this->get('orm.manager')->getDataSet('Settings')
            ->get([ 'opinion_settings', 'items_per_page']);

        $numEditorial = $config['opinion_settings']['total_editorial'];
        $numDirector  = $config['opinion_settings']['total_director'];
        $numOpinions  = $config['items_per_page'];

        if (!empty($config['opinion_settings'])
            && array_key_exists('total_opinions', $config['opinion_settings'])
        ) {
            $numOpinions = $config['opinion_settings']['total_opinions'];
        }

        $authors   = $this->getAuthors();
        $cm        = new \ContentManager();
        $director  = [];
        $editorial = [];
        $where     = '';

        $bloggerIds = array_map(function ($a) {
            return $a->id;
        }, array_filter($authors, function ($a) {
            return !empty($a->is_blog);
        }));

        if (!empty($bloggerIds)) {
            $where .= ' AND opinions.fk_author NOT IN (' . implode(', ', $bloggerIds) . ") ";
        }

        $opinions = $cm->find(
            'Opinion',
            'in_home=1 and content_status=1 and type_opinion=0 ' . $where,
            'ORDER BY position ASC , created DESC LIMIT ' . $numOpinions
        );

        if ($numEditorial > 0) {
            $editorial = $cm->find(
                'Opinion',
                'in_home=1 and content_status=1 and type_opinion=1',
                'ORDER BY position ASC, created DESC LIMIT ' . $numEditorial
            );
        }

        if ($numDirector > 0) {
            $director = $cm->find(
                'Opinion',
                'in_home=1 and content_status=1 and type_opinion=2',
                'ORDER BY position ASC , created DESC LIMIT ' . $numDirector
            );
        }

        if ($numOpinions > 0 && count($opinions) > $numOpinions) {
            $this->get('session')->getFlashBag()->add(
                'notice',
                sprintf(_("You must put %d opinions %s in the frontpage "), $numOpinions, 'opinions')
            );
        }

        if ($numEditorial > 0 && count($editorial) != $numEditorial) {
            $this->get('session')->getFlashBag()->add(
                'notice',
                sprintf(_("You must put %d opinions %s in the frontpage "), $numEditorial, 'editorial')
            );
        }

        if ($numDirector > 0 && count($director) != $numDirector) {
            $this->get('session')->getFlashBag()->add(
                'notice',
                sprintf(_("You must put %d opinions %s in the frontpage "), $numDirector, 'opinion del director')
            );
        }

        return $this->render('opinion/list.tpl', [
            'authors'   => $this->get('api.service.author')->responsify($authors),
            'opinions'  => \Onm\StringUtils::convertToUtf8($opinions),
            'director'  => \Onm\StringUtils::convertToUtf8($director),
            'editorial' => \Onm\StringUtils::convertToUtf8($editorial),
            'type'      => 'frontpage',
            'page'      => $page,
            'home'      => true,
        ]);
    }

    /**
     * Shows the information form for a opinion given its id.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     *
     * @Security("hasExtension('OPINION_MANAGER')
     *     and hasPermission('OPINION_UPDATE')")
     */
    public function showAction(Request $request)
    {
        $id      = $request->query->getDigits('id', null);
        $opinion = $this->get('entity_repository')->find('Opinion', $id);

        // Check if opinion id exists
        if (!is_object($opinion) || is_null($opinion->id)) {
            $this->get('session')->getFlashBag()->add(
                'error',
                sprintf(_('Unable to find the opinion with the id "%d"'), $id)
            );

            return $this->redirect($this->generateUrl('admin_opinions'));
        }

        // Check if you can see others opinions
        if (!$this->get('core.security')->hasPermission('CONTENT_OTHER_UPDATE')
            && $opinion->fk_author != $this->getUser()->id
        ) {
            $this->get('session')->getFlashBag()->add(
                'error',
                _("You can't modify this opinion because you don't have enought privileges.")
            );

            return $this->redirect($this->generateUrl('admin_opinions'));
        }

        $authors = $this->getAuthors();

        if (!empty($opinion->img1)) {
            $photo1 = $this->get('entity_repository')->find('Photo', $opinion->img1);
            $this->view->assign('photo1', $photo1);
        }

        if (!empty($opinion->img2)) {
            $photo2 = $this->get('entity_repository')->find('Photo', $opinion->img2);
            $this->view->assign('photo2', $photo2);
        }

        $extraFields = null;

        if ($this->get('core.security')->hasExtension('es.openhost.module.extraInfoContents')) {
            $extraFields = $this->get('orm.manager')
                ->getDataSet('Settings', 'instance')
                ->get(OpinionsController::EXTRA_INFO_TYPE);
        }

        return $this->render('opinion/new.tpl', [
            'opinion'        => $opinion,
            'authors'        => $authors,
            'enableComments' => $this->get('core.helper.comment')
                ->enableCommentsByDefault(),
            'extra_fields'   => $extraFields,
            'locale'         => $this->get('core.locale')
                ->getRequestLocale('frontend'),
            'tags'           => $this->get('api.service.tag')
                ->getListByIdsKeyMapped($opinion->tag_ids)['items']
        ]);
    }

    /**
     * Handles the form for creating a new opinion.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     *
     * @Security("hasExtension('OPINION_MANAGER')
     *     and hasPermission('OPINION_CREATE')")
     */
    public function createAction(Request $request)
    {
        if ('POST' !== $request->getMethod()) {
            // Fetch categories
            $this->loadCategories();

            $extraFields = null;

            if ($this->get('core.security')->hasExtension('es.openhost.module.extraInfoContents')) {
                $extraFields = $this->get('orm.manager')
                    ->getDataSet('Settings', 'instance')
                    ->get(OpinionsController::EXTRA_INFO_TYPE);
            }

            return $this->render('opinion/new.tpl', [
                'authors'        => $this->getAuthors(),
                'enableComments' => $this->get('core.helper.comment')
                    ->enableCommentsByDefault(),
                'extra_fields'   => $extraFields,
                'locale'         => $this->get('core.locale')
                    ->getLocale('frontend'),
                'tags'           => []
            ]);
        }

        $params  = $request->request->get('params', []);
        $opinion = new \Opinion();

        $contentStatus = $request->request->filter('content_status', '', FILTER_SANITIZE_STRING);
        $inhome        = $request->request->filter('in_home', '', FILTER_SANITIZE_STRING);
        $withComment   = $request->request->filter('with_comment', '', FILTER_SANITIZE_STRING);

        $data = [
            'body'                => $request->request->get('body', ''),
            'content_status'      => (empty($contentStatus)) ? 0 : 1,
            'endtime'             => $request->request->get('endtime', ''),
            'fk_author'           => $request->request->getDigits('fk_author', 0),
            'fk_author_img'       => $request->request->getDigits('fk_author_img'),
            'fk_publisher'        => $this->getUser()->id,
            'fk_user_last_editor' => $request->request->getDigits('fk_user_last_editor'),
            'img1'                => $request->request->getDigits('img1', ''),
            'img1_footer'         =>
                $request->request->filter('img1_footer', '', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES),
            'img2'                => $request->request->getDigits('img2', ''),
            'img2_footer'         =>
                $request->request->filter('img2_footer', '', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES),
            'in_home'             => (empty($inhome)) ? 0 : 1,
            'starttime'           => $request->request->get('starttime', ''),
            'summary'             => $request->request->get('summary', ''),
            'title'               =>
                $request->request->filter('title', '', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES),
            'type_opinion'        => $request->request->filter('type_opinion', '', FILTER_SANITIZE_STRING),
            'with_comment'        => (empty($withComment)) ? 0 : 1,
            'params'              => [
                'only_registered' => array_key_exists('only_registered', $params) ? $params['only_registered'] : '',
            ],
            'tag_ids'             => json_decode($request->request->get('tag_ids', ''), true)
        ];

        $data = $this->loadMetaDataFields($data, $request->request, OpinionsController::EXTRA_INFO_TYPE);

        if ($opinion->create($data)) {
            $this->get('session')->getFlashBag()->add(
                'success',
                _('Opinion successfully created.')
            );

            // Clear caches
            dispatchEventWithParams('opinion.create', [ 'authorId' => $data['fk_author'] ]);
        } else {
            $this->get('session')->getFlashBag()->add(
                'error',
                _('Unable to create the new opinion.')
            );
        }

        return $this->redirect(
            $this->generateUrl('admin_opinion_show', [ 'id' => $opinion->id ])
        );
    }

    /**
     * Updates the opinion information sent by POST.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     *
     * @Security("hasExtension('OPINION_MANAGER')
     *     and hasPermission('OPINION_UPDATE')")
     */
    public function updateAction(Request $request)
    {
        $id     = $request->query->getDigits('id');
        $params = $request->request->get('params', []);

        $opinion = new \Opinion($id);

        if (is_null($opinion->id)) {
            $this->get('session')->getFlashBag()->add(
                'error',
                sprintf(_('Unable to find the opinion with the id "%d"'), $id)
            );

            return $this->redirect($this->generateUrl('admin_opinions'));
        }

        if (!$this->get('core.security')->hasPermission('CONTENT_OTHER_UPDATE')
            && !$opinion->isOwner($this->getUser()->id)
        ) {
            $this->get('session')->getFlashBag()->add(
                'error',
                _("You can't modify this opinion because you don't have enought privileges.")
            );

            return $this->redirect($this->generateUrl('admin_opinions'));
        }

        $contentStatus = $request->request->filter('content_status', '', FILTER_SANITIZE_STRING);
        $inhome        = $request->request->filter('in_home', '', FILTER_SANITIZE_STRING);
        $withComment   = $request->request->filter('with_comment', '', FILTER_SANITIZE_STRING);

        // Check empty data
        if (count($request->request) < 1) {
            $this->get('session')->getFlashBag()->add(
                'error',
                _("Opinion data sent not valid.")
            );

            return $this->redirect($this->generateUrl('admin_opinion_show', [ 'id' => $id ]));
        }

        $data = [
            'body'                => $request->request->get('body', ''),
            'content_status'      => (empty($contentStatus)) ? 0 : 1,
            'endtime'             => $request->request->get('endtime', ''),
            'fk_author'           => $request->request->getDigits('fk_author', 0),
            'fk_author_img'       => $request->request->getDigits('fk_author_img'),
            'fk_publisher'        => $this->getUser()->id,
            'fk_user_last_editor' => $request->request->getDigits('fk_user_last_editor'),
            'id'                  => $id,
            'img1'                => $request->request->getDigits('img1', ''),
            'img1_footer'         =>
                $request->request->filter('img1_footer', '', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES),
            'img2'                => $request->request->getDigits('img2', ''),
            'img2_footer'         =>
                $request->request->filter('img2_footer', '', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES),
            'in_home'             => (empty($inhome)) ? 0 : 1,
            'starttime'           => $request->request->get('starttime', ''),
            'summary'             => $request->request->get('summary', ''),
            'title'               =>
                $request->request->filter('title', '', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES),
            'type_opinion'        => $request->request->filter('type_opinion', '', FILTER_SANITIZE_STRING),
            'with_comment'        => (empty($withComment)) ? 0 : 1,
            'params'              => [
                'only_registered' => array_key_exists('only_registered', $params) ? $params['only_registered'] : '',
            ],
            'tag_ids'             => json_decode($request->request->get('tag_ids', ''), true)
        ];

        $data = $this->loadMetaDataFields($data, $request->request, OpinionsController::EXTRA_INFO_TYPE);

        if ($opinion->update($data)) {
            $this->get('session')->getFlashBag()->add(
                'success',
                _('Opinion successfully updated.')
            );
        } else {
            $this->get('session')->getFlashBag()->add(
                'error',
                _('Unable to update the opinion.')
            );
        }

        return $this->redirect($this->generateUrl('admin_opinion_show', [
            'id' => $opinion->id
        ]));
    }

    /**
     * Change in_home status for one opinion given its id.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     *
     * @Security("hasExtension('OPINION_MANAGER')
     *     and hasPermission('OPINION_HOME')")
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
            $opinion->setInHome($status, $this->getUser()->id);

            $this->get('session')->getFlashBag()->add(
                'success',
                sprintf(_('Successfully changed in home state for the opinion "%s"'), $opinion->title)
            );
        }

        if ($type != 'frontpage') {
            $url = $this->generateUrl('admin_opinions', [
                'type' => $type,
                'page' => $page
            ]);
        } else {
            $url = $this->generateUrl('admin_opinions_frontpage', [
                'page' => $page
            ]);
        }

         return $this->redirect($url);
    }

    /**
     * Lists the available opinions for the frontpage manager.
     *
     * @param  Request $request The request object.
     * @return Response         The response object.
     *
     * @Security("hasExtension('OPINION_MANAGER')")
     */
    public function contentProviderAction(Request $request)
    {
        $categoryId         = $request->query->getDigits('category', 0);
        $page               = $request->query->getDigits('page', 1);
        $itemsPerPage       = 8;
        $frontpageVersionId =
            $request->query->getDigits('frontpage_version_id', null);
        $frontpageVersionId = $frontpageVersionId === '' ?
            null :
            $frontpageVersionId;

        $em  = $this->get('entity_repository');
        $ids = $this->get('api.service.frontpage_version')
            ->getContentIds((int) $categoryId, $frontpageVersionId, 'Opinion');

        $filters = [
            'content_type_name' => [ [ 'value' => 'opinion' ] ],
            'content_status'    => [ [ 'value' => 1 ] ],
            'in_litter'         => [ [ 'value' => 1, 'operator' => '!=' ] ],
            'pk_content'        => [ [ 'value' => $ids, 'operator' => 'NOT IN' ] ]
        ];

        $countOpinions = true;
        $opinions      = $em->findBy($filters, [ 'created' => 'desc' ], $itemsPerPage, $page, 0, $countOpinions);

        $pagination = $this->get('paginator')->get([
            'boundary'    => true,
            'directional' => true,
            'epp'         => $itemsPerPage,
            'page'        => $page,
            'total'       => $countOpinions,
            'route'       => [
                'name'   => 'admin_opinions_content_provider',
                'params' => [ 'category' => $categoryId ]
            ],
        ]);

        return $this->render('opinion/content-provider.tpl', [
            'opinions'   => $opinions,
            'pagination' => $pagination,
        ]);
    }

    /**
     * Lists the latest opinions for the related manager.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     *
     * @Security("hasExtension('OPINION_MANAGER')")
     */
    public function contentProviderRelatedAction(Request $request)
    {
        $page = $request->query->getDigits('page', 1);
        $epp  = $this->get('orm.manager')->getDataSet('Settings')
            ->get('items_per_page') ?: 20;

        $total    = true;
        $opinions = $this->get('entity_repository')->findBy([
            'content_type_name' => [ [ 'value' => 'opinion' ] ],
            'in_litter'         => [ [ 'value' => 1, 'operator' => '!=' ] ]
        ], [ 'created' => 'desc' ], $epp, $page, 0, $total);

        $pagination = $this->get('paginator')->get([
            'boundary'    => true,
            'directional' => true,
            'epp'         => $epp,
            'page'        => $page,
            'total'       => $total,
            'route'       => 'admin_opinions_content_provider_related',
        ]);

        return $this->render(
            'common/content_provider/_container-content-list.tpl',
            [
                'contentType'        => 'Opinion',
                'contents'           => $opinions,
                'pagination'         => $pagination,
                'contentProviderUrl' => $this
                    ->generateUrl('admin_opinions_content_provider_related'),
            ]
        );
    }

    /**
     * Handles the configuration for the opinion manager.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     *
     * @Security("hasExtension('OPINION_MANAGER')
     *     and hasPermission('OPINION_SETTINGS')")
     */
    public function configAction(Request $request)
    {
        $ds = $this->get('orm.manager')->getDataSet('Settings');

        if ('POST' !== $request->getMethod()) {
            return $this->render('opinion/config.tpl', [
                'configs'      => $ds->get([ 'opinion_settings' ]),
                'extra_fields' => $this->get('orm.manager')
                    ->getDataSet('Settings', 'instance')
                    ->get(OpinionsController::EXTRA_INFO_TYPE)
            ]);
        }

        $extra      = $request->request->get('extra-fields');
        $configsRAW = $request->request->get('opinion_settings');

        $configs = [
            'opinion_settings' => [
                'total_director'        => filter_var($configsRAW['total_director'], FILTER_VALIDATE_INT),
                'total_editorial'       => filter_var($configsRAW['total_editorial'], FILTER_VALIDATE_INT),
                'total_opinions'        => filter_var($configsRAW['total_opinions'], FILTER_VALIDATE_INT),
                'total_opinion_authors' => filter_var($configsRAW['total_opinion_authors'], FILTER_VALIDATE_INT),
                'blog_orderFrontpage'   => filter_var($configsRAW['blog_orderFrontpage'], FILTER_SANITIZE_STRING),
                'blog_itemsFrontpage'   => filter_var($configsRAW['blog_itemsFrontpage'], FILTER_VALIDATE_INT),
            ],
            'extraInfoContents.OPINION_MANAGER' => json_decode($extra, true)
        ];

        try {
            $ds->set($configs);

            $this->get('session')->getFlashBag()
                ->add('success', _('Settings saved successfully.'));

            return $this->redirect($this->generateUrl('admin_opinions_config'));
        } catch (\Exception $e) {
            $this->get('session')->getFlashBag()
                ->add('error', _('Unable to save the settings.'));

            return $this->redirect($this->generateUrl('admin_opinions_config'));
        }
    }

    /**
     * Previews an opinion in frontend by sending the opinion info by POST.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     *
     * @Security("hasExtension('OPINION_MANAGER')
     *     and hasPermission('OPINION_ADMIN')")
     */
    public function previewAction(Request $request)
    {
        $opinion    = new \Opinion();
        $cm         = new \ContentManager();
        $this->view = $this->get('core.template');

        $this->view->setCaching(0);
        $this->get('core.locale')->setContext('frontend');

        $opinionContents = $request->request->filter('contents');

        // Fetch all opinion properties and generate a new object
        foreach ($opinionContents as $value) {
            if (isset($value['name']) && !empty($value['name'])) {
                $opinion->{$value['name']} = $value['value'];
            }
        }

        $opinion->tag_ids = json_decode($opinion->tag_ids);

        // Set a dummy Id for the opinion if doesn't exists
        if (empty($opinion->pk_article) && empty($opinion->id)) {
            $opinion->pk_opinion = 0;
            $opinion->id         = 0;
        }

        list($positions, $advertisements) =
            \Frontend\Controller\OpinionsController::getAds('inner');

        try {
            if (!empty($opinion->fk_author)) {
                $opinion->author = $this->get('api.service.author')
                    ->getItem($opinion->fk_author);
            }
        } catch (\Exception $e) {
        }

        // Rescato esta asignación para que genere correctamente el enlace a frontpage de opinion
        $opinion->author_name_slug = \Onm\StringUtils::getTitle($opinion->name);

        $machineSuggestedContents = $this->get('automatic_contents')
            ->searchSuggestedContents('opinion', "pk_content <> $opinion->id", 4);

        // Get author slug for suggested opinions
        foreach ($machineSuggestedContents as &$suggest) {
            $element = new \Opinion($suggest['pk_content']);

            $suggest['author_name_slug'] = "author";
            $suggest['uri']              = $element->uri;

            if (!empty($element->author)) {
                $suggest['author_name']      = $element->author;
                $suggest['author_name_slug'] =
                    \Onm\StringUtils::getTitle($element->author);
            }
        }

        // Associated media code --------------------------------------
        $photo = '';
        if (isset($opinion->img2) && ($opinion->img2 > 0)) {
            $photo = new \Photo($opinion->img2);
        }

        // Fetch the other opinions for this author
        if ($opinion->type_opinion == 1) {
            $where         = ' opinions.type_opinion = 1';
            $opinion->name = 'Editorial';
            $this->view->assign('actual_category', 'editorial');
        } elseif ($opinion->type_opinion == 2) {
            $where         = ' opinions.type_opinion = 2';
            $opinion->name = 'Director';
        } else {
            $where = ' opinions.fk_author=' . (int) $opinion->fk_author;
        }

        $otherOpinions = $cm->find(
            'Opinion',
            $where . ' AND `pk_opinion` <>' . $opinion->id . ' AND content_status=1',
            ' ORDER BY created DESC LIMIT 0,9'
        );

        foreach ($otherOpinions as &$otOpinion) {
            $otOpinion->author           = $opinion->author;
            $otOpinion->author_name_slug = $opinion->author_name_slug;
            $otOpinion->uri              = $otOpinion->uri;
        }

        $this->view->assign([
            'ads_positions'  => $positions,
            'advertisements' => $advertisements,
            'opinion'        => $opinion,
            'content'        => $opinion,
            'other_opinions' => $otherOpinions,
            'author'         => $opinion->author,
            'contentId'      => $opinion->id,
            'photo'          => $photo,
            'suggested'      => $machineSuggestedContents,
            'tags'           => $this->get('api.service.tag')
                ->getListByIdsKeyMapped($opinion->tag_ids)['items']
        ]);

        $this->get('session')->set(
            'last_preview',
            $this->view->fetch('opinion/opinion.tpl')
        );

        return new Response('OK');
    }

    /**
     * Description of this action.
     *
     * @return Response The response object.
     *
     * @Security("hasExtension('OPINION_MANAGER')
     *     and hasPermission('OPINION_ADMIN')")
     */
    public function getPreviewAction()
    {
        $session = $this->get('session');
        $content = $session->get('last_preview');

        $session->remove('last_preview');

        return new Response($content);
    }

    /**
     * Common code for all the actions
     */
    public function loadCategories()
    {
        $this->ccm = \ContentCategoryManager::get_instance();

        list($this->parentCategories, $this->subcat, $this->categoryData)
            = $this->ccm->getArraysMenu();

        $this->view->assign([
            'allcategorys' => $this->parentCategories,
        ]);
    }

    /**
     * Returns the list of authors.
     *
     * @return array The list of authors.
     */
    protected function getAuthors()
    {
        $response = $this->get('api.service.author')
            ->getList('order by name asc');

        return $this->get('data.manager.filter')
            ->set($response['items'])
            ->filter('mapify', [ 'key' => 'id'])
            ->get();
    }
}
