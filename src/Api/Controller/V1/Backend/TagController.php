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

use Common\Core\Annotation\Security;
use Common\Core\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Common\Core\Component\Validator\Validator;

/**
 * Lists and displays tags.
 */
class TagController extends Controller
{
    /**
     * Deletes an tag.
     *
     * @param integer $id The tag id.
     *
     * @return JsonResponse The response object.
     *
     * @Security("hasPermission('TAG_DELETE')")
     */
    public function deleteAction($id)
    {
        $msg = $this->get('core.messenger');

        $this->get('api.service.tag')->deleteItem($id);
        $msg->add(_('Item deleted successfully'), 'success');

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }

    /**
     * Deletes the selected tags.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     *
     * @Security("hasPermission('TAG_DELETE')")
     */
    public function deleteSelectedAction(Request $request)
    {
        $ids     = $request->request->get('ids', []);
        $msg     = $this->get('core.messenger');
        $deleted = $this->get('api.service.tag')->deleteList($ids);

        if ($deleted > 0) {
            $msg->add(
                sprintf(_('%s items deleted successfully'), $deleted),
                'success'
            );
        }

        if ($deleted !== count($ids)) {
            $msg->add(sprintf(
                _('%s items could not be deleted successfully'),
                count($ids) - $deleted
            ), 'error');
        }

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }

    /**
     * Returns a list of contents in JSON format.
     *
     * @param  Request      $request     The request object.
     * @param  string       $contentType Content type name.
     * @return JsonResponse              The response object.
     *
     * @Security("hasPermission('TAG_ADMIN')")
     */
    public function listAction(Request $request)
    {
        $ts  = $this->get('api.service.tag');
        $oql = $request->query->get('oql', '');

        $response = $ts->getList($ts->replaceSearchBySlug($oql));

        return new JsonResponse([
            'items' => $ts->responsify($response['items']),
            'total' => $response['total'],
            'extra' => $this->getExtraData($response['items'])
        ]);
    }

    /**
     * Saves a new tag.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     *
     * @Security("hasPermission('TAG_CREATE')")
     */
    public function saveAction(Request $request)
    {
        $msg  = $this->get('core.messenger');
        $data = $request->request->all();

        if (array_key_exists('slug', $data)) {
            $msg->add(_('Wrong parameter slug'), 'error');
            return new JsonResponse($msg->getMessages(), $msg->getCode());
        }

        $ts = $this->get('api.service.tag');

        $data['slug'] = $ts->createSearchableWord($data['name']);

        $tag = $ts->createItem($data);

        $msg->add(_('Item saved successfully'), 'success', 201);

        $response = new JsonResponse($msg->getMessages(), $msg->getCode());
        $response->headers->set(
            'Location',
            $this->generateUrl(
                'api_v1_backend_tags_list',
                [ 'id' => $tag->id ]
            )
        );

        return $response;
    }

    /**
     * Returns an tag.
     *
     * @param integer $id the tag id.
     *
     * @return JsonResponse The response object.
     *
     * @Security("hasPermission('TAG_ADMIN')")
     */
    public function showAction($id)
    {
        $ss   = $this->get('api.service.tag');
        $item = $ss->getItem($id);

        return new JsonResponse([
            'item'  => $ss->responsify($item)
        ]);
    }

    /**
     * Updates the tag information given its id and the new information.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     *
     * @Security("hasPermission('TAG_ADMIN')")
     */
    public function updateAction(Request $request, $id)
    {
        $msg = $this->get('core.messenger');
        $tag = $request->request->all();

        if (array_key_exists('slug', $tag)) {
            $msg->add(_('Wrong parameter slug'), 'error');
            return new JsonResponse($msg->getMessages(), $msg->getCode());
        }

        $ts = $this->get('api.service.tag');

        $tag['slug'] = $ts->createSearchableWord($tag['name']);

        $ts->updateItem($id, $tag);

        $msg->add(_('Item saved successfully'), 'success');

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }

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
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     */
    public function suggesterAction(Request $request, $languageId, $tag)
    {
        $ts     = $this->get('api.service.tag');
        $tagAux = $ts->createSearchableWord($tag);

        $msg = $this->get('core.messenger');
        if (empty($tagAux)) {
            $msg->add(_('Invalid tag'), 'error');
            return new JsonResponse($msg->getMessages(), $msg->getCode());
        }

        $response = $ts->getList('language_id = "' . $languageId . '" and slug ~ "' . $tagAux . '%" limit 25');

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
     *
     * @Security("hasPermission('TAG_ADMIN')")
     */
    public function showConfAction()
    {
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
     *
     * @Security("hasPermission('TAG_ADMIN')")
     */
    public function updateConfAction(Request $request)
    {
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
     * Loads extra data related to the given contents.
     *
     * @param boolean $all Whether to use 'All' or 'Select...' option.
     *
     * @return array Array of extra data.
     */
    private function getExtraData($tagList)
    {
        $multilanguage = in_array(
            'es.openhost.module.multilanguage',
            $this->get('core.instance')->activated_modules
        );

        $ls      = $this->get('core.locale');
        $locale  = $ls->getLocale('frontend');
        $locales = $multilanguage ?
            $this->getLanguages($ls->getAvailableLocales('frontend')) :
            [['key' => $locale, 'value' => $ls->getSupportedLocales('frontend')[$locale]]];

        $extraData = [
            'numberOfContents' => $this->get('api.service.tag')->getNumContentsRel($tagList),
            'locales'          => $locales
        ];

        return $extraData;
    }

    /**
     * Transform the language object in a array
     *
     * @param object $languages Transform the object languages in a array
     *
     * @return array Array with the languages.
     */
    private function getLanguages($languages)
    {
        $arrayLanguages = [];
        foreach ($languages as $key => $value) {
            $arrayLanguages[] = ['key' => $key, 'value' => $value];
        }
        return $arrayLanguages;
    }
}
