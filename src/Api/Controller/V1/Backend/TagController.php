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

use Api\Controller\V1\ApiController;
use Common\Core\Component\Validator\Validator;
use Common\Model\Entity\Tag;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Lists and displays tags.
 */
class TagController extends ApiController
{
    /**
     * {@inheritdoc}
     */
    protected $extension = 'es.openhost.module.tags';

    /**
     * {@inheritdoc}
     */
    protected $getItemRoute = 'api_v1_backend_tag_get_item';

    /**
     * {@inheritdoc}
     */
    protected $permissions = [
        'create' => 'TAG_CREATE',
        'delete' => 'TAG_DELETE',
        'list'   => 'TAG_ADMIN',
        'patch'  => 'TAG_UPDATE',
        'save'   => 'TAG_CREATE',
        'show'   => 'TAG_UPDATE',
        'update' => 'TAG_UPDATE',
    ];

    /**
     * {@inheritdoc}
     */
    protected $service = 'api.service.tag';

    /**
     * Get the tag config.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     */
    public function getConfigAction()
    {
        $this->checkSecurity($this->extension, $this->getActionPermission('list'));

        $settings = $this->get('orm.manager')
            ->getDataSet('Settings')
            ->get([ 'tags_maxItems', 'tags_maxResults' ]);

        $extra = [ 'blacklist_tag' => $this->get('core.validator')->getConfig(Validator::BLACKLIST_RULESET_TAGS) ];

        if (!empty($extra['blacklist_tag'])) {
            $settings = array_merge($settings, $extra);
        }

        if (array_key_exists('tags_maxItems', $settings)) {
            $settings['tags_maxItems'] = (int) $settings['tags_maxItems'];
        }

        if (array_key_exists('tags_maxResults', $settings)) {
            $settings['tags_maxResults'] = (int) $settings['tags_maxResults'];
        }

        return new JsonResponse($settings);
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
        $this->checkSecurity($this->extension, $this->getActionPermission('list'));

        $settings = $request->request->all();

        if (!array_key_exists('blacklist_tag', $settings)
            || empty($settings['blacklist_tag'])
        ) {
            $settings['blacklist_tag'] = null;
        }

        $msg = $this->get('core.messenger');

        try {
            $this->get('core.validator')->setConfig(Validator::BLACKLIST_RULESET_TAGS, $settings['blacklist_tag']);
            $this->get('orm.manager')->getDataSet('Settings', 'instance')->set($settings);
            $msg->add(_('Item saved successfully'), 'success');
        } catch (\Exception $e) {
            $msg->add(_('Unable to save settings'), 'error');
            $this->get('error.log')->error($e->getMessage());
        }

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }

    /**
     * Checks if the information in the request is valid to create a new Tag.
     *
     * @param Request $request The request object.
     *
     * @return Response The response object.
     */
    public function validateItemAction(Request $request)
    {
        $msg  = $this->get('core.messenger');
        $data = $request->query->all();

        $data['slug'] = $this->get('data.manager.filter')
            ->set($data['name'])
            ->filter('slug')
            ->get();

        $item = new Tag($data);

        try {
            $this->get('api.validator.tag')->validate($item);
        } catch (\Exception $e) {
            $msg->add($e->getMessage(), 'error', 400);
        }

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }

    /**
     * {@inheritdoc}
     */
    protected function getExtraData($items = [])
    {
        $extraData = [
            'stats'   => $this->get('api.service.tag')->getStats($items),
            'locale'  => $this->get('core.helper.locale')->getConfiguration(),
        ];

        return $extraData;
    }
}
