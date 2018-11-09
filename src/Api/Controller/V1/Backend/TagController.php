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
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Common\Core\Component\Validator\Validator;

/**
 * Lists and displays tags.
 */
class TagController extends ApiController
{
    /**
     * {@inheritdoc}
     */
    protected $service = 'api.service.tag';

    /**
     * Get suggested word.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     */
    public function validNewTagAction(Request $request)
    {
        $ts   = $this->get('api.service.tag');
        $text = $request->query->get('text', null);
        $msg  = $this->get('core.messenger');
        if (empty($text)) {
            $msg->add(_('Invalid tag'), 'success');
            return new JsonResponse($msg->getMessages(), $msg->getCode());
        }

        $languageId = $request->query->get('languageId', null);
        $valid      = !is_null($languageId) && $ts->isValidNewTag($text, $languageId);

        return new JsonResponse([
            'valid' => $valid
        ]);
    }

    /**
     * Get suggested tags for some word.
     *
     * @param string $languageId The tag language.
     * @param string $tag        The partial tag language.
     *
     * @return JsonResponse The response object.
     */
    public function suggesterAction($languageId, $tag)
    {
        $ts  = $this->get('api.service.tag');
        $oql = 'language_id = "%s" and name ~ "%s%%" limit 25';

        $response = $ts->getList(sprintf($oql, $languageId, $tag));

        return new JsonResponse([
            'items' => $ts->responsify($response['items'])
        ]);
    }

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
        return new JsonResponse([
            'items' => $ts->getTagsAndNewTags($languageId, $tags)
        ]);
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
        $this->checkSecurity(null, 'TAG_ADMIN');

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
        $this->checkSecurity(null, 'TAG_ADMIN');

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
            'stats'   => $this->get('api.service.tag')->getNumContentsRel($items),
            'locale'  => $ls->getLocale('frontend'),
            'locales' => $locales
        ];

        return $extraData;
    }
}
