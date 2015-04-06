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

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Backend\Annotation\CheckModuleAccess;
use Onm\Framework\Controller\Controller;
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
            array(
                'category'     => $category,
                'subcat'       => $this->subcat,
                'allcategorys' => $this->parentCategories,
                'datos_cat'    => $this->categoryData,
                'timezone'     => $timezone->getName()
            )
        );
    }

    /**
     * Lists all the available polls.
     *
     * @return void
     *
     * @Security("has_role('POLL_ADMIN')")
     *
     * @CheckModuleAccess(module="POLL_MANAGER")
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
     * @return void
     *
     * @Security("has_role('POLL_ADMIN')")
     *
     * @CheckModuleAccess(module="POLL_MANAGER")
     */
    public function widgetAction()
    {
        $configurations = s::get('poll_settings');
        if (array_key_exists('total_widget', $configurations)) {
            $totalWidget = $configurations['total_widget'];
        } else {
            $totalWidget = 0;
        }

        return $this->render(
            'poll/list.tpl',
            array(
                'category'              => 'widget',
                'total_elements_widget' => $totalWidget,
            )
        );
    }

    /**
     * Handles the form for create new polls.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     *
     * @Security("has_role('POLL_CREATE')")
     *
     * @CheckModuleAccess(module="POLL_MANAGER")
     */
    public function createAction(Request $request)
    {
        if ('POST' == $request->getMethod()) {
            $poll = new \Poll();

            $data = array(
                'title'          => $request->request->filter('title', '', FILTER_SANITIZE_STRING),
                'subtitle'       => $request->request->filter('subtitle', '', FILTER_SANITIZE_STRING),
                'description'    => $request->request->filter('description', '', FILTER_SANITIZE_STRING),
                'metadata'       => $request->request->filter('metadata', '', FILTER_SANITIZE_STRING),
                'favorite'       => $request->request->getDigits('favorite', 0),
                'with_comment'   => $request->request->getDigits('with_comment', 0),
                'visualization'  => $request->request->getDigits('visualization', 0),
                'category'       => $request->request->filter('category', '', FILTER_SANITIZE_STRING),
                'content_status' => $request->request->filter('content_status', 0, FILTER_SANITIZE_STRING),
                'item'           => json_decode($request->request->get('parsedAnswers')),
                'params'         => $request->request->get('params'),
            );
            $poll = $poll->create($data);

            if (!empty($poll->id)) {
                $this->get('session')->getFlashBag()->add(
                    'success',
                    _('Poll successfully created.')
                );

                return $this->redirect(
                    $this->generateUrl('admin_poll_show', ['id' => $poll->id])
                );
            } else {
                $this->get('session')->getFlashBag()->add(
                    'error',
                    _('Unable to create the new poll.')
                );

                return $this->redirect(
                    $this->generateUrl('admin_polls', ['category' => $data['category']])
                );
            }


        } else {
            return $this->render('poll/new.tpl', array('commentsConfig' => s::get('comments_config')));
        }
    }

    /**
     * Shows the poll information form.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     *
     * @Security("has_role('POLL_UPDATE')")
     *
     * @CheckModuleAccess(module="POLL_MANAGER")
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
            array(
                'poll'  => $poll,
                'items' => $poll->items,
                'commentsConfig' => s::get('comments_config'),
            )
        );
    }

    /**
     * Updates the poll information.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     *
     * @Security("has_role('POLL_UPDATE')")
     *
     * @CheckModuleAccess(module="POLL_MANAGER")
     */
    public function updateAction(Request $request)
    {
        $id = $request->query->getDigits('id');

        if (count($request->request) < 1) {
            $this->get('session')->getFlashBag()->add('error', _("Poll data sent not valid."));

            return $this->redirect($this->generateUrl('admin_poll_show', array('id' => $id)));
        }

        $poll = new \Poll($id);
        // Check empty data
        if ($poll->id == null) {
            $this->get('session')->getFlashBag()->add(
                'error',
                sprintf(_('Unable to find a poll with the id "%s".'), $id)
            );

            return $this->redirect(
                $this->generateUrl('admin_polls', array('category' => $data['category']))
            );
        }


        $data = array(
            'id'             => $id,
            'title'          => $request->request->filter('title', '', FILTER_SANITIZE_STRING),
            'subtitle'       => $request->request->filter('subtitle', '', FILTER_SANITIZE_STRING),
            'description'    => $request->request->filter('description', '', FILTER_SANITIZE_STRING),
            'visualization'  => $request->request->filter('visualization', '', FILTER_SANITIZE_STRING),
            'metadata'       => $request->request->filter('metadata', '', FILTER_SANITIZE_STRING),
            'favorite'       => $request->request->getDigits('favorite', 0),
            'with_comment'   => $request->request->getDigits('with_comment', 0),
            'category'       => $request->request->filter('category', '', FILTER_SANITIZE_STRING),
            'content_status' => $request->request->getDigits('content_status', 0),
            'item'           => json_decode($request->request->get('parsedAnswers')),
            'params'         => $request->request->get('params'),
        );

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
            $this->generateUrl('admin_poll_show', array('id' => $poll->id))
        );
    }

    /**
     * Delete a poll given its id.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     *
     * @Security("has_role('POLL_DELETE')")
     *
     * @CheckModuleAccess(module="POLL_MANAGER")
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
                    array(
                        'category' => $category,
                        'page'     => $page
                    )
                )
            );
        } else {
            return new Response('Ok', 200);
        }
    }

    /**
     * Render the content provider for polls.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     *
     * @CheckModuleAccess(module="POLL_MANAGER")
     */
    public function contentProviderAction(Request $request)
    {
        $categoryId   = $request->query->getDigits('category', 0);
        $page         = $request->query->getDigits('page', 1);
        $itemsPerPage = 8;

        $em  = $this->get('entity_repository');
        $ids = $this->get('frontpage_repository')->getContentIdsForHomepageOfCategory();

        $filters = array(
            'content_type_name' => array(array('value' => 'poll')),
            'content_status'    => array(array('value' => 1)),
            'in_litter'         => array(array('value' => 1, 'operator' => '!=')),
            'pk_content'        => array(array('value' => $ids, 'operator' => 'NOT IN'))
        );

        $polls      = $em->findBy($filters, array('created' => 'desc'), $itemsPerPage, $page);
        $countPolls = $em->countBy($filters);

        // Build the pager
        $pagination = $this->get('paginator')->create([
            'elements_per_page' => $itemsPerPage,
            'total_items'       => $countPolls,
            'base_url'          => $this->generateUrl(
                'admin_polls_content_provider',
                array('category' => $categoryId)
            ),
        ]);

        return $this->render(
            'poll/content-provider.tpl',
            array(
                'polls' => $polls,
                'pager'  => $pagination,
            )
        );
    }

    /**
     * Lists all the polls withing a category for the related manager.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     *
     * @CheckModuleAccess(module="POLL_MANAGER")
     */
    public function contentProviderRelatedAction(Request $request)
    {
        $categoryId   = $request->query->getDigits('category', 0);
        $page         = $request->query->getDigits('page', 1);
        $itemsPerPage = s::get('items_per_page') ?: 20;

        $em       = $this->get('entity_repository');
        $category = $this->get('category_repository')->find($categoryId);

        $filters = array(
            'content_type_name' => array(array('value' => 'poll')),
            'in_litter'         => array(array('value' => 1, 'operator' => '!='))
        );

        if ($categoryId != 0) {
            $filters['category_name'] = array(array('value' => $category->name));
        }

        $polls      = $em->findBy($filters, array('created' => 'desc'), $itemsPerPage, $page);
        $countPolls = $em->countBy($filters);


        $pagination = $this->get('paginator')->create([
            'elements_per_page' => $itemsPerPage,
            'total_items'       => $countPolls,
            'delta'             => 1,
            'base_url'          => $this->generateUrl(
                'admin_polls_content_provider_related',
                array('category' => $categoryId)
            ),
        ]);

        return $this->render(
            'common/content_provider/_container-content-list.tpl',
            array(
                'contentType'           => 'Poll',
                'contents'              => $polls,
                'contentTypeCategories' => $this->parentCategories,
                'category'              => $categoryId,
                'pagination'            => $pagination->links,
                'contentProviderUrl'    => $this->generateUrl('admin_polls_content_provider_related'),
            )
        );
    }

    /**
     * Handles the configuration for the polls module.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     *
     * @Security("has_role('POLL_SETTINGS')")
     *
     * @CheckModuleAccess(module="POLL_MANAGER")
     */
    public function configAction(Request $request)
    {
        if ('POST' == $request->getMethod()) {
            $settingsRAW = $request->request->get('poll_settings');
            $data = array(
                'poll_settings' => array(
                    'typeValue'    => $settingsRAW['typeValue'] ?: 0,
                    'heightPoll'   => $settingsRAW['heightPoll'] ?: 0,
                    'widthPoll'    => $settingsRAW['widthPoll'] ?: 0,
                    'total_widget' => $settingsRAW['total_widget'] ?: 0,
                    'widthWidget'  => $settingsRAW['widthWidget'] ?: 0,
                    'heightWidget' => $settingsRAW['heightWidget'] ?: 0,
                )
            );

            foreach ($data as $key => $value) {
                s::set($key, $value);
            }
            $this->get('session')->getFlashBag()->add('success', _('Settings saved successfully.'));

            return $this->redirect($this->generateUrl('admin_polls_config'));
        } else {
            $configurations = s::get(array('poll_settings',));

            return $this->render(
                'poll/config.tpl',
                array('configs' => $configurations,)
            );
        }
    }
}
