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
    protected function loadExtraData($contents)
    {
        if (empty($contents)) {
            return [];
        }

        $extra = [];
        $ids   = [];

        foreach ($contents as $content) {
            $ids[] = $content->fk_author;
            $ids[] = $content->fk_publisher;
            $ids[] = $content->fk_user_last_editor;
        }

        $ids = array_unique($ids);

        if (($key = array_search(0, $ids)) !== false) {
            unset($ids[$key]);
        }

        if (($key = array_search(null, $ids)) !== false) {
            unset($ids[$key]);
        }

        $extra['authors'] = [];
        if (!empty($ids)) {
            $converter = $this->get('orm.manager')->getConverter('User');
            $users     = $this->get('orm.manager')->getRepository('User')
                ->findBy(sprintf('id in [%s]', implode(',', $ids)));

            foreach ($users as $user) {
                $user->eraseCredentials();

                $extra['authors'][$user->id] = $converter->responsify($user->getData());
            }
        }

        $converter  = $this->get('orm.manager')->getConverter('Category');
        $categories = $this->get('orm.manager')
            ->getRepository('Category')
            ->findBy();

        $categories = $converter->responsify($categories);

        foreach ($categories as $category) {
            $extra['categories'][$category['pk_content_category']] = $category;
        }

        return $extra;
    }
}
