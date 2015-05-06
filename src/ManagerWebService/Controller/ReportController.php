<?php

/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ManagerWebService\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

use Onm\Framework\Controller\Controller;

class ReportController extends Controller
{
    /**
     * Returns the list of users as JSON.
     *
     * @param Request $request The request object.
     *
     * @return JsonResponse The response object.
     */
    public function listAction(Request $request)
    {
        $results = [
            [
                'id'          => '1',
                'title'       => _('Instances not used in the last month'),
                'description' =>  _('Report with the list of instances not used in the last month')
            ],
            [
                'id'          => '2',
                'title'       => _('Instances created in the last month'),
                'description' =>  _('Report with the list of new instances created in last month')
            ]
        ];

        return new JsonResponse([ 'results'  => $results ]);
    }
}
