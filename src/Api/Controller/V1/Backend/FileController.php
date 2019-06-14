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

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

class FileController extends ContentOldController
{
    /**
     * {@inheritdoc}
     */
    protected $extension = 'FILE_MANAGER';

    /**
     * The route name to generate URL from when creating a new item.
     *
     * @var string
     */
    protected $getItemRoute = 'api_v1_backend_file_show';

    /**
     * Returns a list with the file name and id of files in JSON format.
     *
     * @param  Request      $request     The request object.
     * @param  string       $contentType Content type name.
     * @return JsonResponse              The response object.
     */
    public function autocompleteAction(Request $request)
    {
        $search   = $request->query->get('query');
        $order    = [ 'path' => 'desc' ];
        $criteria = [
            'content_type_name' => [ ['value' => 'attachment', 'operator' => '='] ],
            'in_litter'         => [ ['value' => 0, 'operator' => '='] ],
            'path'              => [ ['value' => '%' . $search . '%', 'operator' => 'like'] ],
            'join'              => [ [
                'table'      => 'attachments',
                'pk_content' => [ [ 'value' => 'pk_attachment', 'field' => true ] ]
            ] ]
        ];

        $results = $this->get('entity_repository')->findBy($criteria, $order, 10);
        $results = array_map(function ($file) {
            return ['id' => $file->id, 'filename' => basename($file->path)];
        }, $results);

        return new JsonResponse([ 'results' => $results ]);
    }

    /**
     * {@inheritDoc}
     */
    protected function getExtraData($items = null)
    {
        return array_merge(parent::getExtraData($items), [
            'categories' => $this->getCategories($items),
            'tags'       => $this->getTags($items)
        ]);
    }
}
