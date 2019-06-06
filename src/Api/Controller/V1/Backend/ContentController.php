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

        $msg  = $this->get('core.messenger');
        $data = $request->request->all();
        $item = $this->get($this->service)->createItem($data);

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

        $msg  = $this->get('core.messenger');
        $data = $request->request->all();

        $this->get($this->service)->updateItem($id, $data);

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
            'keys'             => $this->get($this->service)->getL10nKeys(),
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
}
