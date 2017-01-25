<?php
/**
 * Handles the actions for the poll manager
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

use Common\Core\Annotation\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Common\Core\Controller\Controller;
use Onm\Settings as s;

/**
 * Handles the actions for the poll manager
 *
 * @package Backend_Controllers
 **/
class PollsController extends Controller
{
    /**
     * Common code for all the actions.
     */
    public function init()
    {
        $contentType = \ContentManager::getContentTypeIdFromName('poll');

        $category = $this->request->query->filter(INPUT_GET, 0, FILTER_SANITIZE_STRING);

        $ccm = \ContentCategoryManager::get_instance();
        list($this->parentCategories, $this->subcat, $this->categoryData) =
            $ccm->getArraysMenu($category, $contentType);

        if (empty($category)) {
            $category ='home';
        }

        $timezones = \DateTimeZone::listIdentifiers();
        $timezone  = new \DateTimeZone($timezones[s::get('time_zone', 'UTC')]);

        $this->view->assign(
            [
                'category'     => $category,
                'subcat'       => $this->subcat,
                'allcategorys' => $this->parentCategories,
                'datos_cat'    => $this->categoryData,
                'timezone'     => $timezone->getName()
            ]
        );
    }

    /**
     * Lists all the available polls.
     *
     * @return Response The response object.
     *
     * @Security("hasExtension('POLL_MANAGER')
     *     and hasPermission('POLL_ADMIN')")
     */
    public function listAction()
    {
        $categories = [ [ 'name' => _('All'), 'value' => -1 ] ];

        foreach ($this->parentCategories as $key => $category) {
            $categories[] = [
                'name' => $category->title,
                'value' => $category->name
            ];

            foreach ($this->subcat[$key] as $subcategory) {
                $categories[] = [
                    'name' => '&rarr; ' . $subcategory->title,
                    'value' => $subcategory->name
                ];
            }
        }

        return $this->render(
            'poll/list.tpl',
            [ 'categories' => $categories ]
        );
    }

    /**
     * Lists all the polls in the widget.
     *
     * @return Response The response object.
     *
     * @Security("hasExtension('POLL_MANAGER')
     *     and hasPermission('POLL_ADMIN')")
     */
    public function widgetAction()
    {
        $configurations = s::get('poll_settings');
        $totalWidget = 0;

        if (array_key_exists('total_widget', $configurations)) {
            $totalWidget = $configurations['total_widget'];
        }

        return $this->render(
            'poll/list.tpl',
            [
                'category'              => 'widget',
                'total_elements_widget' => $totalWidget,
            ]
        );
    }

    /**
     * Handles the form for create new polls.
     *
     * @param  Request  $request The request object.
     *
     * @return Response The response object.
     *
     * @Security("hasExtension('POLL_MANAGER')
     *     and hasPermission('POLL_CREATE')")
     */
    public function createAction(Request $request)
    {
        if ('POST' == $request->getMethod()) {
            $poll = new \Poll();

            $data = [
                'title'          => $request->request->filter('title', '', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES),
                'subtitle'       => $request->request->filter('subtitle', '', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES),
                'description'    => $request->request->filter('description', '', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES),
                'metadata'       => \Onm\StringUtils::normalizeMetadata($request->request->filter('metadata', '', FILTER_SANITIZE_STRING)),
                'favorite'       => $request->request->getDigits('favorite', 0),
                'with_comment'   => $request->request->getDigits('with_comment', 0),
                'visualization'  => $request->request->getDigits('visualization', 0),
                'category'       => $request->request->filter('category', '', FILTER_SANITIZE_STRING),
                'content_status' => $request->request->filter('content_status', 0, FILTER_SANITIZE_STRING),
                'item'           => json_decode($request->request->get('parsedAnswers')),
                'params'         => $request->request->get('params', []),
            ];
            $poll = $poll->create($data);

            if (!empty($poll->id)) {
                $this->get('session')->getFlashBag()->add(
                    'success',
                    _('Poll successfully created.')
                );

                return $this->redirect(
                    $this->generateUrl('admin_poll_show', ['id' => $poll->id])
                );
            }

            $this->get('session')->getFlashBag()->add(
                'error',
                _('Unable to create the new poll.')
            );

            return $this->redirect(
                $this->generateUrl('admin_polls', ['category' => $data['category']])
            );
        }

        return $this->render('poll/new.tpl', [ 'commentsConfig' => s::get('comments_config') ]);
    }

    /**
     * Shows the poll information form.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     *
     * @Security("hasExtension('POLL_MANAGER')
     *     and hasPermission('POLL_UPDATE')")
     */
    public function showAction(Request $request)
    {
        $id = $request->query->getDigits('id', null);

        $poll = new \Poll($id);
        if (is_null($poll->id)) {
            $this->get('session')->getFlashBag()->add(
                'error',
                sprintf(_('Unable to find the poll with the id "%d"'), $id)
            );

            return $this->redirect($this->generateUrl('admin_polls'));
        }
        if (is_string($poll->params)) {
            $poll->params = unserialize($poll->params);
        }

        return $this->render(
            'poll/new.tpl',
            [
                'poll'  => $poll,
                'items' => $poll->items,
                'commentsConfig' => s::get('comments_config'),
            ]
        );
    }

    /**
     * Updates the poll information.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     *
     * @Security("hasExtension('POLL_MANAGER')
     *     and hasPermission('POLL_UPDATE')")
     */
    public function updateAction(Request $request)
    {
        $id = $request->query->getDigits('id');

        if (count($request->request) < 1) {
            $this->get('session')->getFlashBag()->add('error', _("Poll data sent not valid."));

            return $this->redirect($this->generateUrl('admin_poll_show', [ 'id' => $id ]));
        }

        $poll = new \Poll($id);
        // Check empty data
        if ($poll->id == null) {
            $this->get('session')->getFlashBag()->add(
                'error',
                sprintf(_('Unable to find a poll with the id "%s".'), $id)
            );

            return $this->redirect(
                $this->generateUrl('admin_polls', [ 'category' => $data['category'] ])
            );
        }

        $data = [
            'id'             => $id,
            'title'          => $request->request->filter('title', '', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES),
            'subtitle'       => $request->request->filter('subtitle', '', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES),
            'description'    => $request->request->filter('description', '', FILTER_SANITIZE_STRING, FILTER_FLAG_NO_ENCODE_QUOTES),
            'visualization'  => $request->request->filter('visualization', '', FILTER_SANITIZE_STRING),
            'metadata'       => \Onm\StringUtils::normalizeMetadata($request->request->filter('metadata', '', FILTER_SANITIZE_STRING)),
            'favorite'       => $request->request->getDigits('favorite', 0),
            'with_comment'   => $request->request->getDigits('with_comment', 0),
            'category'       => $request->request->filter('category', '', FILTER_SANITIZE_STRING),
            'content_status' => $request->request->getDigits('content_status', 0),
            'item'           => json_decode($request->request->get('parsedAnswers')),
            'params'         => $request->request->get('params'),
        ];

        if ($poll->update($data)) {
            $this->get('session')->getFlashBag()->add(
                'success',
                _('Poll successfully updated.')
            );
        } else {
            $this->get('session')->getFlashBag()->add(
                'error',
                _('Unable to update the poll.')
            );
        }

        return $this->redirect(
            $this->generateUrl('admin_poll_show', [ 'id' => $poll->id ])
        );
    }

    /**
     * Delete a poll given its id.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     *
     * @Security("hasExtension('POLL_MANAGER')
     *     and hasPermission('POLL_DELETE')")
     */
    public function deleteAction(Request $request)
    {
        $id       = $request->query->getDigits('id');
        $category = $request->query->filter('category', 'all', FILTER_SANITIZE_STRING);
        $page     = $request->query->getDigits('page', 1);

        if (empty($id)) {
            $this->get('session')->getFlashBag()->add(
                'error',
                _('You must give an id for delete a poll.')
            );
        }

        $poll = new \Poll($id);
        $poll->delete($id);
        $this->get('session')->getFlashBag()->add(
            'success',
            _("Poll deleted successfully.")
        );

        if (!$request->isXmlHttpRequest()) {
            return $this->redirect(
                $this->generateUrl(
                    'admin_polls',
                    [
                        'category' => $category,
                        'page'     => $page
                    ]
                )
            );
        }

        return new Response('Ok', 200);
    }

    /**
     * Render the content provider for polls.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     *
     * @Security("hasExtension('POLL_MANAGER')")
     */
    public function contentProviderAction(Request $request)
    {
        $categoryId   = $request->query->getDigits('category', 0);
        $page         = $request->query->getDigits('page', 1);
        $itemsPerPage = 8;

        $em  = $this->get('entity_repository');
        $ids = $this->get('frontpage_repository')->getContentIdsForHomepageOfCategory((int)$categoryId);

        $filters = [
            'content_type_name' => [ [ 'value' => 'poll' ] ],
            'content_status'    => [ [ 'value' => 1 ] ],
            'in_litter'         => [ [ 'value' => 1, 'operator' => '!=' ] ],
            'pk_content'        => [ [ 'value' => $ids, 'operator' => 'NOT IN' ] ]
        ];

        $polls      = $em->findBy($filters, [ 'created' => 'desc' ], $itemsPerPage, $page);
        $countPolls = $em->countBy($filters);

        // Build the pagination
        $pagination = $this->get('paginator')->get([
            'boundary'    => true,
            'directional' => true,
            'epp'         => $itemsPerPage,
            'page'        => $page,
            'total'       => $countPolls,
            'route'       => [
                'name'   => 'admin_polls_content_provider',
                'params' => [ 'category' => $categoryId ]
            ],
        ]);

        return $this->render(
            'poll/content-provider.tpl',
            [
                'polls'      => $polls,
                'pagination' => $pagination,
            ]
        );
    }

    /**
     * Lists all the polls withing a category for the related manager.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     *
     * @Security("hasExtension('POLL_MANAGER')")
     */
    public function contentProviderRelatedAction(Request $request)
    {
        $categoryId   = $request->query->getDigits('category', 0);
        $page         = $request->query->getDigits('page', 1);
        $itemsPerPage = s::get('items_per_page') ?: 20;

        $em       = $this->get('entity_repository');
        $category = $this->get('category_repository')->find($categoryId);

        $filters = [
            'content_type_name' => [ [ 'value' => 'poll' ] ],
            'in_litter'         => [ [ 'value' => 1, 'operator' => '!=' ] ]
        ];

        if ($categoryId != 0) {
            $filters['category_name'] = [ [ 'value' => $category->name ] ];
        }

        $polls      = $em->findBy($filters, [ 'created' => 'desc' ], $itemsPerPage, $page);
        $countPolls = $em->countBy($filters);

        $pagination = $this->get('paginator')->get([
            'epp'   => $itemsPerPage,
            'total' => $countPolls,
            'page'  => $page,
            'route' => [
                'name'  => 'admin_polls_content_provider_related',
                'param' => [ 'category' => $categoryId ]
            ],
        ]);

        return $this->render(
            'common/content_provider/_container-content-list.tpl',
            [
                'contentType'           => 'Poll',
                'contents'              => $polls,
                'contentTypeCategories' => $this->parentCategories,
                'category'              => $categoryId,
                'pagination'            => $pagination,
                'contentProviderUrl'    => $this->generateUrl('admin_polls_content_provider_related'),
            ]
        );
    }

    /**
     * Handles the configuration for the polls module.
     *
     * @param  Request  $request The request object.
     *
     * @return Response The response object.
     *
     * @Security("hasExtension('POLL_MANAGER')
     *     and hasPermission('POLL_SETTINGS')")
     */
    public function configAction(Request $request)
    {
        if ('POST' == $request->getMethod()) {
            $settingsRAW = $request->request->get('poll_settings');
            $data = [
                'poll_settings' => [
                    'typeValue'    => $settingsRAW['typeValue'] ?: 0,
                    'heightPoll'   => $settingsRAW['heightPoll'] ?: 0,
                    'widthPoll'    => $settingsRAW['widthPoll'] ?: 0,
                    'total_widget' => $settingsRAW['total_widget'] ?: 0,
                    'widthWidget'  => $settingsRAW['widthWidget'] ?: 0,
                    'heightWidget' => $settingsRAW['heightWidget'] ?: 0,
                ]
            ];

            foreach ($data as $key => $value) {
                s::set($key, $value);
            }
            $this->get('session')->getFlashBag()->add('success', _('Settings saved successfully.'));

            return $this->redirect($this->generateUrl('admin_polls_config'));
        }

        $configurations = s::get([ 'poll_settings', ]);

        return $this->render(
            'poll/config.tpl',
            [ 'configs' => $configurations, ]
        );
    }
}
