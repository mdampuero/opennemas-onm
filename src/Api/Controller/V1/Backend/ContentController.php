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

class ContentController extends ApiController
{
    /**
     * The API service name.
     *
     * @var string
     */
    protected $service = 'api.service.content';

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

        $data         = $request->request->all();
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
        $security   = $this->get('core.security');
        $converter  = $this->get('orm.manager')->getConverter('Category');
        $categories = $this->get('orm.manager')
            ->getRepository('Category')
            ->findBy('internal_category = 1');

        $categories = array_filter($categories, function ($category) use ($security) {
            return $security->hasCategory($category->pk_content_category);
        });

        $extra = [
            'categories'       => $converter->responsify($categories),
            'related_contents' => $this->getRelatedContents($items),
            'tags'             => $this->getTagsFromItems($items),
            'template_vars'    => [
                'media_dir' => $this->get('core.instance')->getMediaShortPath() . '/images',
            ],
        ];

        return array_merge($extra, $this->getLocaleData('frontend'));
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
                $tagIds = array_merge($tagIds, $item->tags);
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
}
