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
use Common\Core\Controller\Controller;

/**
 * Lists and displays files for content.
 */
class FileController extends Controller
{
    /**
     * Returns a list with the file name and id of files in JSON format.
     *
     * @param  Request      $request     The request object.
     * @param  string       $contentType Content type name.
     * @return JsonResponse              The response object.
     */
    public function autocompleteAction(Request $request)
    {
        $elementsPerPage = $request->query->getDigits('elements_per_page', 10);
        $search          = $request->query->get('search');
        $page            = 1;

        $criteria['content_type_name'] = [['value' => 'attachment', 'operator' => '=']];
        $criteria['in_litter'] = [['value' => 0, 'operator' => '=']];
        $criteria['path'] = [['value' => '%' . $search . '%', 'operator' => 'like']];

        $criteria['join'] = [[
                'table'      => 'attachments',
                'pk_content' => [ [ 'value' => 'pk_attachment', 'field' => true ] ]
            ]
        ];

        $em = $this->get('entity_repository');

        $results = $em->findBy($criteria, '`path` desc', $elementsPerPage);
        $results = array_map(function ($file) {
            return ['id' => $file->id, 'fileName' => basename($file->path)];
        }, $results);

        return new JsonResponse([
            'results' => $results,
        ]);
    }
}
