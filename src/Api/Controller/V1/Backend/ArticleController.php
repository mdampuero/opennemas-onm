<?php
/**
 * This file is part of the Onm package.
 *
 * (c)  OpenHost S.L. <developers@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Api\Controller\V1\Backend;

use Common\Core\Annotation\Security;
use Common\Core\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ArticleController extends Controller
{
    /**
     * Returns a list of contents in JSON format.
     *
     * @param Request $request     The request object.
     * @param string  $contentType Content type name.
     *
     * @return JsonResponse The response object.
     *
     * @Security("hasPermission('ARTICLE_ADMIN')")
     */
    public function listAction(Request $request)
    {
        $oql = $request->query->get('oql', '');

        list($criteria, $order, $epp, $page) =
            $this->get('core.helper.oql')->getFiltersFromOql($oql);

        $em      = $this->get('entity_repository');
        $total   = $em->countBy($criteria);
        $results = $em->findBy($criteria, $order, $epp, $page);

        $results = \Onm\StringUtils::convertToUtf8($results);

        return new JsonResponse([
            'elements_per_page' => $epp,
            'extra'             => $this->loadExtraData($results),
            'page'              => $page,
            'results'           => $results,
            'total'             => $total,
        ]);
    }

    /**
     * Loads extra data related to the given contents.
     *
     * @param  array $contents Array of contents.
     * @return array           Array of extra data.
     */
    protected function loadExtraData()
    {
        $extra = [];

        $converter  = $this->get('orm.manager')->getConverter('Category');
        $categories = $this->get('orm.manager')
            ->getRepository('Category')
            ->findBy();

        $extra['categories'] = $converter->responsify($categories);
        array_unshift($extra['categories'], [ 'pk_content_category' => null, 'title' => _('All') ]);

        $converter = $this->get('orm.manager')->getConverter('User');
        $users     = $this->get('orm.manager')->getRepository('User')
            ->findBy('fk_user_group regexp "^3($|,)|,\s*3\s*,|(^|,)\s*3$"');

        foreach ($users as $user) {
            $user->eraseCredentials();
        }

        $extra['users'] = $converter->responsify($users);
        array_unshift($extra['users'], [ 'id' => null, 'name' => _('All') ]);

        return $extra;
    }
}
