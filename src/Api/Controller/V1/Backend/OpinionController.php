<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Api\Controller\V1\Backend;

use Api\Exception\GetItemException;
use Common\Model\Entity\Content;
use Common\Model\Entity\Opinion;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class OpinionController extends ContentController
{
    /**
     * {@inheritdoc}
     */
    protected $extension = 'OPINION_MANAGER';

    /**
     * {@inheritdoc}
     */
    protected $getItemRoute = 'api_v1_backend_opinion_get_item';

    /**
     * {@inheritdoc}
     */
    protected $permissions = [
        'create' => 'OPINION_CREATE',
        'delete' => 'OPINION_DELETE',
        'patch'  => 'OPINION_UPDATE',
        'update' => 'OPINION_UPDATE',
        'list'   => 'OPINION_ADMIN',
        'save'   => 'OPINION_CREATE',
        'show'   => 'OPINION_UPDATE',
    ];

    protected $propertyName = 'opinion';

    protected $translations = [
        [
            'name' => 'tags',
            'title' => 'Tags'
        ],
        [
            'name' => 'slug',
            'title' => 'Slug'
        ],
        [
            'name' => 'bodyLink',
            'title' => 'External link'
        ],
        [
            'name' => 'schedule',
            'title' => 'Schedule'
        ],
        [
            'name' => 'featuredFrontpage',
            'title' => 'Featured in frontpage'
        ],
        [
            'name' => 'featuredInner',
            'title' => 'Featured in inner'
        ]
    ];

    /**
     * {@inheritdoc}
     */
    protected $service = 'api.service.opinion';

    /**
     * Get the tag config.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     */
    public function getConfigAction()
    {
        $this->checkSecurity($this->extension, 'OPINION_SETTINGS');

        $settings = $this->get('orm.manager')
            ->getDataSet('Settings')
            ->get('extraInfoContents.OPINION_MANAGER');

        return new JsonResponse([ 'extrafields' => $settings ]);
    }

    /**
     * Description of this action.
     *
     * @return Response The response object.
     */
    public function getPreviewAction()
    {
        $this->checkSecurity($this->extension, $this->getActionPermission('ADMIN'));

        $session = $this->get('session');
        $content = $session->get('last_preview');

        $session->remove('last_preview');

        return new Response($content);
    }

    /**
     * Saves configuration for tags.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     */
    public function saveConfigAction(Request $request)
    {
        $this->checkSecurity($this->extension, 'OPINION_SETTINGS');

        $extra    = $request->request->get('extrafields');
        $settings = [ 'extraInfoContents.OPINION_MANAGER' => $extra ];

        $msg = $this->get('core.messenger');

        try {
            $this->get('orm.manager')->getDataSet('Settings')->set($settings);
            $msg->add(_('Item saved successfully'), 'success');
        } catch (\Exception $e) {
            $msg->add(_('Unable to save settings'), 'error');
            $this->get('error.log')->error($e->getMessage());
        }

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }

    /**
     * Previews an opinion in frontend by sending the opinion info by POST.
     *
     * @param Request $request The request object.
     *
     * @return Response The response object.
     */
    public function savePreviewAction(Request $request)
    {
        $this->checkSecurity($this->extension, $this->getActionPermission('ADMIN'));

        $this->get('core.locale')->setContext('frontend')
            ->setRequestLocale($request->get('locale'));

        $opinion = new Content([ 'pk_content' => 0 ]);

        $data = $request->request->filter('item');
        $data = json_decode($data, true);

        foreach ($data as $key => $value) {
            if (isset($value) && !empty($value)) {
                $opinion->{$key} = $value;
            }
        }

        $opinion->with_comment = 0;

        $opinion = $this->get('data.manager.filter')->set($opinion)
            ->filter('localize', [ 'keys'   => $this->get($this->service)->getL10nKeys('opinion') ])
            ->get();

        $this->view = $this->get('core.template');
        $this->view->setCaching(0);
        $this->getAdvertisements();

        try {
            if (!empty($opinion->fk_author)) {
                $opinion->author = $this->get('api.service.author')
                    ->getItem($opinion->fk_author);
            }
        } catch (\Exception $e) {
        }

        $params = [
            'item'           => $opinion,
            'content'        => $opinion,
            'author'         => $opinion->author,
            'contentId'      => $opinion->pk_content
        ];

        $this->view->assign($params);

        $template = (!empty($opinion->author) && $opinion->author->is_blog == 1)
            ? 'opinion/blog_inner.tpl'
            : 'opinion/opinion.tpl';

        $this->get('session')->set(
            'last_preview',
            $this->view->fetch($template)
        );

        return new Response('OK');
    }

    /**
     * {@inheritdoc}
     */
    protected function getExtraData($items = null)
    {
        $extra = parent::getExtraData($items);

        $extraFields = null;

        if ($this->get('core.security')->hasExtension('es.openhost.module.extraInfoContents')) {
            $extraFields = $this->get('orm.manager')
                ->getDataSet('Settings', 'instance')
                ->get('extraInfoContents.OPINION_MANAGER');
        }

        return array_merge([
            'extra_fields' => $extraFields,
            'tags'         => $this->getTags($items),
            'formSettings'  => [
                'name'             => $this->propertyName,
                'expansibleFields' => $this->translateFields($this->translations)
            ]
        ], $extra);
    }

    /**
     * {@inheritdoc}
     */
    public function getL10nKeys()
    {
        return $this->get($this->service)->getL10nKeys('opinion');
    }
}
