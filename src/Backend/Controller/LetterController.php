<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Backend\Controller;

use Api\Exception\GetListException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class LetterController extends BackendController
{
    /**
     * The extension name required by this controller.
     *
     * @var string
     */
    protected $extension = 'LETTER_MANAGER';

    /**
     * The list of permissions for every action.
     *
     * @var type
     */
    protected $permissions = [
        'create' => 'LETTER_CREATE',
        'update' => 'LETTER_UPDATE',
        'list'   => 'LETTER_ADMIN',
        'show'   => 'LETTER_UPDATE',
    ];

    /**
     * The resource name.
     */
    protected $resource = 'letter';

    /**
     * Lists the available Letters for the frontpage manager.
     *
     * @param  Request  $request The request object.
     * @return Response          The response object.
     *
     */
    public function contentProviderAction(Request $request)
    {
        $this->checkSecurity($this->extension);

        $page         = $request->query->getDigits('page', 1);
        $category     = $request->query->getDigits('category', 0);
        $itemsPerPage = 8;
        $oql          = 'content_type_name = "letter" and in_litter != 1 and content_status = 1 ';

        if (!empty($category)) {
            $oql .= sprintf('and category_id = %d ', $category);
        }

        try {
            $oql .= 'order by created desc limit ' . $itemsPerPage;

            if ($page > 1) {
                $oql .= ' offset ' . ($page - 1) * $itemsPerPage;
            }

            $context = $this->get('core.locale')->getContext();
            $this->get('core.locale')->setContext('frontend');

            $response = $this->get('api.service.letter')->getList($oql);

            $this->get('core.locale')->setContext($context);

            $pagination = $this->get('paginator')->get([
                'boundary'    => true,
                'directional' => true,
                'epp'         => 8,
                'maxLinks'    => 5,
                'page'        => $page,
                'total'       => $response['total'],
                'route'       => [
                    'name'   => 'backend_letters_content_provider'
                ],
            ]);

            return $this->render('letter/content-provider.tpl', [
                'letters'    => $response['items'],
                'pagination' => $pagination,
            ]);
        } catch (GetListException $e) {
        }
    }
}
