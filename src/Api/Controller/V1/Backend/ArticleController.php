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

use Api\Exception\GetItemException;
use Common\Core\Annotation\Security;
use Common\Core\Controller\Controller;
use Common\Model\Entity\Content;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ArticleController extends Controller
{
    /**
     * Returns the list of paramters needed to create a new article.
     *
     * @return JsonResponse The response object.
     */
    public function createAction()
    {
        return new JsonResponse([ 'extra' => $this->getExtraData() ]);
    }

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
        $results = $em->findBy($criteria, $order, $epp, $page);
        $total   = $em->countBy($criteria);

        $results = \Onm\StringUtils::convertToUtf8($results);

        return new JsonResponse([
            'elements_per_page' => $epp,
            'extra'             => $this->getExtraData(),
            'page'              => $page,
            'results'           => $results,
            'total'             => $total,
        ]);
    }

    /**
     * Returns the article information.
     *
     * @param integer $id The article id.
     *
     * @return JsonResponse The response object.
     *
     * @Security("hasExtension('ARTICLE_MANAGER')
     *     and hasPermission('ARTICLE_UPDATE')")
     */
    public function showAction($id)
    {
        $article = $this->get('entity_repository')->find('Article', $id);

        if (is_null($article->id)) {
            return new JsonResponse(
                sprintf(_('Unable to find the article with the id "%d"'), $id),
                400
            );
        }

        $extra = $this->getExtraData($article);

        if (!empty($article->tags)) {
            $ts = $this->get('api.service.tag');

            $extra['tags'] = $ts->responsify(
                $ts->getListByIds($article->tags)['items']
            );
        }

        return new JsonResponse([ 'article' => $article, 'extra' => $extra ]);
    }

    /**
     * Loads extra data related to the given contents.
     *
     * @return array Array of extra data.
     */
    protected function getExtraData($article = null)
    {
        $extra      = [ 'tags' => [] ];
        $categories = $this->get('api.service.category')->getList()['items'];

        $extra['categories'] = $this->get('api.service.category')
            ->responsify($categories);

        $ss = $this->get('api.service.subscription');
        $as = $this->get('api.service.author');

        $subscriptions = $ss->getList('enabled = 1 order by name asc');
        $response      = $as->getList('order by name asc');

        $extra['subscriptions'] = $ss->responsify($subscriptions['items']);
        $extra['authors']       = $as->responsify($response['items']);
        $extra['keys']          = \Article::getL10nKeys();
        $extra['locale']        = $this->get('core.helper.locale')
            ->getConfiguration();

        if ($this->get('core.security')->hasExtension('es.openhost.module.extraInfoContents')) {
            $extra['moduleFields'] = $this->get('orm.manager')
                ->getDataSet('Settings', 'instance')
                ->get('extraInfoContents.ARTICLE_MANAGER');
        }

        $extra['with_comment'] = $this->get('core.helper.comment')->enableCommentsByDefault();

        $extra['related'] = $this->getRelated($article);

        $extra = array_merge($extra, $this->getPhotos($article));
        $extra = array_merge($extra, $this->getVideos($article));

        return $extra;
    }

    /**
     * Returns the list of photos linked to the article.
     *
     * @param Article $article The article.
     *
     * @return array The list of photos linked to the article.
     */
    protected function getPhotos($article)
    {
        if (empty($article)) {
            return [];
        }

        $service = $this->get('api.service.photo');
        $extra   = [];
        $keys    = [ 'img1', 'img2' ];

        foreach ($keys as $key) {
            if (empty($article->{$key})) {
                continue;
            }
            try {
                $photo       = $service->getItem($article->{$key});
                $extra[$key] = $service->responsify($photo);
            } catch (GetItemException $e) {
            }
        }

        if (!is_array($article->params)
            || !array_key_exists('imageHome', $article->params)
            || empty($article->params['imageHome'])
        ) {
            return $extra;
        }
        try {
            $photo              = $service->getItem($article->params['imageHome']);
            $extra['imageHome'] = $service->responsify($photo);
        } catch (GetItemException $e) {
        }

        return $extra;
    }

    /**
     * Returns the list of contents linked to the article in frontpage, inner
     * and home.
     *
     * @param Article $article The article.
     *
     * @return array The list of contents linked to the article.
     */
    protected function getRelated(&$article = null)
    {
        if (empty($article) || empty($article->related_contents)) {
            return [];
        }

        $repository = $this->get('entity_repository');
        $contents   = array_filter(array_map(function ($a) use ($repository) {
            $item = $repository->find($a['content_type_name'], $a['target_id']);
            return $item instanceof Content ?
                $this->get('api.service.content')->responsify($item) :
                $item;
        }, $article->related_contents));

        $contents = $this->get('data.manager.filter')->set($contents)
            ->filter('mapify', [ 'key' => 'pk_content' ])
            ->get();

        return $contents;
    }

    /**
     * Returns the list of videos linked to the article.
     *
     * @param Article $article The article.
     *
     * @return array The list of videos linked to the article.
     */
    protected function getVideos($article)
    {
        if (empty($article)) {
            return [];
        }

        $service = $this->get('api.service.content');
        $extra   = [];
        $keys    = [ 'fk_video', 'fk_video2' ];

        foreach ($keys as $key) {
            if (!empty($article->{$key})) {
                try {
                    $extra[$key] = $service->responsify($service->getItem($article->{$key}));
                } catch (GetItemException $e) {
                }
            }
        }

        return $extra;
    }
}
