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

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ContentOldController extends ContentController
{
    /**
     * The API service name.
     *
     * @var string
     */
    protected $service = 'api.service.content_old';

    /**
     * Returns the content id
     *
     * @param Content $item the item
     *
     * @return integer
     **/
    public function getItemId($item)
    {
        return $item->pk_content;
    }

    /**
     * Returns a list of items.
     *
     * @param Request $request The request object.
     *
     * @return array The list of items and all extra information.
     */
    public function listAction(Request $request)
    {
        $this->checkSecurity($this->extension, $this->getActionPermission('list'));

        $us  = $this->get($this->service);
        $oql = $request->query->get('oql', '');

        $response = $us->getList($oql);

        return [
            'items'      => $response['items'],
            'total'      => $response['total'],
            'extra'      => $this->getExtraData($response['items']),
            'o-filename' => $this->filename,
        ];
    }

    /**
     * Saves a new item.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     */
    public function saveAction(Request $request)
    {
        $this->checkSecurity($this->extension, $this->getActionPermission('save'));

        $msg = $this->get('core.messenger');

        $data         = $request->request->all();
        $data['tags'] = $this->parseTags($data['tags']);

        $item = $this->get($this->service)
            ->createItem($data);
        $msg->add(_('Item saved successfully'), 'success', 201);

        $response = new JsonResponse($msg->getMessages(), $msg->getCode());

        if (!empty($this->getItemRoute)) {
            $response->headers->set('Location', $this->generateUrl(
                $this->getItemRoute,
                [ 'id' => $this->getItemId($item) ]
            ));
        }

        return $response;
    }

    /**
     * Updates the item information given its id and the new information.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     */
    public function updateAction(Request $request, $id)
    {
        $this->checkSecurity($this->extension, $this->getActionPermission('update'));

        $msg = $this->get('core.messenger');

        $data = $request->request->all();

        $data['tags'] = $this->parseTags($data['tags']);

        $this->get($this->service)
            ->updateItem($id, $data);

        $msg->add(_('Item saved successfully'), 'success');

        return new JsonResponse($msg->getMessages(), $msg->getCode());
    }

    /**
     * Returns a list of extra data
     *
     * @return array
     **/
    protected function getExtraData($items = null)
    {
        return [
            'related_contents' => $this->getRelatedContents($items),
            'tags'             => $this->getTagsFromItems($items),
            'keys'             => $this->get($this->service)->getL10nKeys(),
            'authors'          => $this->getAuthors(),
            'locale'           => $this->get('core.helper.locale')->getConfiguration(),
            'template_vars'    => [
                'media_dir' => $this->get('core.instance')->getMediaShortPath() . '/images',
            ],
        ];
    }

    /**
     * Returns the list of photos linked to the article.
     *
     * @param Content $content The content.
     *
     * @return array The list of photos linked to the content.
     */
    protected function getRelatedContents($items)
    {
        return [];
    }

    /**
     * Returns the list of tag ids for a list of items or a individual item
     *
     * @param array|Content $items One Content object or a list of Content objects
     *
     * @return array
     **/
    private function getTagsFromItems($items = null)
    {
        if (empty($items)) {
            return [];
        }

        if (is_object($items)) {
            $items = [ $items ];
        }

        $tagIds = [];
        if (is_array($items)) {
            foreach ($items as $item) {
                $tagIds = array_merge($tagIds, $item->tag_ids);
            }
        }

        return $this->get('api.service.tag')
            ->getListByIdsKeyMapped($tagIds)['items'];
    }

    /**
     * Parses the tags provided from the request and transforms them into
     * ids
     *
     * @param array $tags The lis tof tag objects
     * @return array
     **/
    private function parseTags($tags = [])
    {
        if (empty($tags)) {
            return [];
        }

        $ts = $this->get('api.service.tag');

        $ids = [];
        foreach ($tags as $tag) {
            if (!array_key_exists('id', $tag) || !is_numeric($tag['id'])) {
                unset($tag['id']);

                try {
                    $tag = $ts->responsify($ts->createItem($tag));
                } catch (\Exception $e) {
                    continue;
                }
            }

            $ids[] = (int) $tag['id'];
        }

        return array_unique($ids);
    }

    /**
     * Returns the lit of authors
     *
     * @return array the list of authors
     **/
    public function getAuthors()
    {
        $us = $this->get('api.service.author');

        $response = $us->getList('order by name asc');
        $authors  = $this->get('data.manager.filter')
            ->set($response['items'])
            ->filter('mapify', [ 'key' => 'id'])
            ->get();

        return $us->responsify($authors);
    }
}
