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
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class OpinionController extends BackendController
{
    /**
     * The extension name required by this controller.
     *
     * @var string
     */
    protected $extension = 'OPINION_MANAGER';

    /**
     * The list of permissions for every action.
     *
     * @var type
     */
    protected $permissions = [
        'create' => 'EVENT_CREATE',
        'update' => 'EVENT_UPDATE',
        'list'   => 'EVENT_ADMIN',
        'show'   => 'EVENT_CREATE',
    ];

    /**
     * {@inheritdoc}
     */
    protected $groups = [
        'preview' => 'opinion_inner'
    ];

    /**
     * The resource name.
     *
     * @var string
     */
    protected $resource = 'opinion';

    /**
     * The name of the setting to save extra field configuration.
     *
     * @var string
     */
    const EXTRA_INFO_TYPE = 'extraInfoContents.OPINION_MANAGER';

    /**
     * Shows the information form for a opinion given its id.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     *
     * @Security("hasExtension('OPINION_MANAGER')
     *     and hasPermission('OPINION_UPDATE')")
     */
    public function showAction(Request $request, $id)
    {

        $this->checkSecurity($this->extension, $this->getActionPermission('update'));

        $params = [ 'id' => $id ];

        if ($this->get('core.helper.locale')->hasMultilanguage()) {
            $params['locale'] = $request->query->get('locale');
        }

        return $this->render($this->resource . '/item.tpl', $params);

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
        return $this->render('opinion/item.tpl');
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
                'name'   => 'backend_opinions_content_provider',
                'params' => [ 'category' => $categoryId ]
            ],
        ]);

        return $this->render('opinion/content-provider.tpl', [
            'opinions'   => $opinions,
            'pagination' => $pagination,
        ]);
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
                    ->get(self::EXTRA_INFO_TYPE)
            ]);
        }

        $extra      = $request->request->get('extra-fields');
        $configsRAW = $request->request->get('opinion_settings');

        $configs = [
            'opinion_settings' => [
                'total_opinions'        => filter_var($configsRAW['total_opinions'], FILTER_VALIDATE_INT),
                'blog_orderFrontpage'   => filter_var($configsRAW['blog_orderFrontpage'], FILTER_SANITIZE_STRING),
                'blog_itemsFrontpage'   => filter_var($configsRAW['blog_itemsFrontpage'], FILTER_VALIDATE_INT),
            ],
            'extraInfoContents.OPINION_MANAGER' => json_decode($extra, true)
        ];

        try {
            $ds->set($configs);

            $this->get('session')->getFlashBag()
                ->add('success', _('Settings saved successfully.'));

            return $this->redirect($this->generateUrl('backend_opinions_config'));
        } catch (\Exception $e) {
            $this->get('session')->getFlashBag()
                ->add('error', _('Unable to save the settings.'));

            return $this->redirect($this->generateUrl('backend_opinions_config'));
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
        $this->get('core.locale')->setContext('frontend')
            ->setRequestLocale($request->get('locale'));

        $opinion     = new \Opinion();
        $cm          = new \ContentManager();
        $opinion->id = 0;

        $data = $request->request->filter('item');
        var_dump($data);die();
        foreach ($data as $value) {
            if (isset($value['name']) && !empty($value['name'])) {
                $opinion->{$value['name']} = $value['value'];
            }
        }

        $this->view = $this->get('core.template');
        $this->view->setCaching(0);

        $opinion->tag_ids = json_decode($opinion->tag_ids);

        list($positions, $advertisements) = $this->getAdvertisements();

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

        $params = [
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
        ];

        $this->view->assign($params);

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

    /**
     * This method load from the request the metadata fields,
     *
     * @param mixed   $data Data where load the metadata fields.
     * @param Request $postReq Request where the metadata are.
     * @param string  $type type of the extra field
     *
     * @return array
     */
    protected function loadMetaDataFields($data, $postReq, $type)
    {
        if (!$this->get('core.security')->hasExtension('es.openhost.module.extraInfoContents')) {
            return $data;
        }

        // If I don't have the extension, I don't check the settings
        $groups = $this->get('orm.manager')
            ->getDataSet('Settings', 'instance')
            ->get($type);

        if (!is_array($groups)) {
            return $data;
        }

        foreach ($groups as $group) {
            foreach ($group['fields'] as $field) {
                if ($postReq->get($field['key'], null) == null) {
                    continue;
                }

                $data[$field['key']] = $postReq->get($field['key']);
            }
        }
        return $data;
    }
}
