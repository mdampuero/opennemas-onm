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
use Common\ORM\Entity\Tag;
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
    protected $getItemRoute = 'api_v1_backend_tag_show';

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
     * Get suggested tags for some word.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     */
    public function autoSuggesterAction(Request $request)
    {
        $tags       = $request->query->get('tags', null);
        $languageId = $request->query->get('languageId', null);
        $ts         = $this->get('api.service.tag');
        $items      = $ts->getTagsAndNewTags($languageId, $tags);

        return new JsonResponse([
            'items' => $ts->responsify($items)
        ]);
    }

    /**
     * Checks if the information in the request is valid to create a new Tag.
     *
     * @param Request $request The request object.
     *
     * @return Response The response object.
     */
    public function validateAction(Request $request)
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
            $msg->add('error', $e->getMessage(), 400);
        }

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }

    /**
     * Get the tag config.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     */
    public function showConfAction()
    {
        $this->checkSecurity($this->extension, $this->getActionPermission('list'));

        return new JsonResponse([
            'blacklist_tag' => $this->get('core.validator')
                ->getConfig(Validator::BLACKLIST_RULESET_TAGS)
        ]);
    }

    /**
     * Update tag config.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     */
    public function updateConfAction(Request $request)
    {
        $this->checkSecurity($this->extension, $this->getActionPermission('list'));

        $blacklistConf = $request->request->all();

        if (!is_array($blacklistConf) ||
            !array_key_exists('blacklist_tag', $blacklistConf) ||
            empty($blacklistConf['blacklist_tag'])
        ) {
            $blacklistConf = ['blacklist_tag' => null];
        }

        $msg = $this->get('core.messenger');
        try {
            $this->get('core.validator')->setConfig(
                Validator::BLACKLIST_RULESET_TAGS,
                $blacklistConf['blacklist_tag']
            );
            $msg->add(_('Item saved successfully'), 'success');
        } catch (\Exception $e) {
            $msg->add(
                _('Unable to save settings'),
                'error'
            );
            $this->get('error.log')->error($e->getMessage());
        }
        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }

    /**
     * {@inheritdoc}
     */
    protected function getExtraData($items = [])
    {
        $ls      = $this->get('core.locale');
        $locales = [ $ls->getLocale('frontend') => $ls->getLocaleName('frontend') ];

        $multilanguage = in_array(
            'es.openhost.module.multilanguage',
            $this->get('core.instance')->activated_modules
        );

        if ($multilanguage) {
            $locales = $ls->getAvailableLocales('frontend');
        }

        $extraData = [
            'stats'   => $this->get('api.service.tag')->getStats($items),
            'locale'  => $ls->getLocale('frontend'),
            'locales' => $locales
        ];

        return $extraData;
    }
}
