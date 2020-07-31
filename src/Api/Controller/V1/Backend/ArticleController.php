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
     * Returns the list of paramters needed to create a new article.
     *
     * @return JsonResponse The response object.
     */
    public function createAction()
    {
        return new JsonResponse([ 'extra' => $this->getExtraData(false) ]);
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

        $extra = $this->getExtraData(false);
        $extra = array_merge($extra, $this->getPhotos($article));
        $extra = array_merge($extra, $this->getVideos($article));
        $extra = array_merge($extra, $this->getAlbums($article));
        $extra = array_merge($extra, $this->getRelated($article));

        if (!empty($article->tags)) {
            $ts = $this->get('api.service.tag');

            $extra['tags'] = $ts->responsify(
                $ts->getListByIds($article->tags)['items']
            );
        }

        return new JsonResponse([ 'article' => $article, 'extra' => $extra ]);
    }

    /**
     * Returns the list of albums linked to the article.
     *
     * @param Article $article The article.
     *
     * @return array The list of albums linked to the article.
     */
    protected function getAlbums($article)
    {
        if (!$this->get('core.security')->hasExtension('CRONICAS_MODULES')) {
            return [];
        }

        $em    = $this->get('entity_repository');
        $extra = [];
        $keys  = [ 'withGallery', 'withGalleryInt', 'withGalleryHome' ];

        foreach ($keys as $key) {
            if (array_key_exists($key, $article->params)
                && !empty($article->params[$key])
            ) {
                $extra[$key] = \Onm\StringUtils::convertToUtf8(
                    $em->find('Album', $article->params[$key])
                );
            }
        }

        return $extra;
    }

    /**
     * Loads extra data related to the given contents.
     *
     * @param boolean $all Whether to use 'All' or 'Select...' option.
     *
     * @return array Array of extra data.
     */
    protected function getExtraData($all = true)
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
        $service = $this->get('api.service.photo');
        $extra   = [];
        $keys    = [ 'img1', 'img2' ];

        foreach ($keys as $key) {
            if (empty($article->{$key})) {
                continue;
            }
            try {
                $photo = $service->getItem($article->{$key});

                if (!empty($photo)) {
                    $extra[$key] = $service->responsify($photo);
                }
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
            $photo = $service->getItem($article->params['imageHome']);

            if (!empty($photo)) {
                $extra['imageHome'] = $service->responsify($photo);
            }
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
    protected function getRelated(&$article)
    {
        $em    = $this->get('entity_repository');
        $extra = [];
        $fm    = $this->get('data.manager.filter');
        $keys  = [ 'frontpage', 'inner', 'home' ];
        $rm    = $this->get('related_contents');

        foreach ($keys as $key) {
            $name = 'related' . ucfirst(str_replace('page', '', $key));

            if ($key === 'home'
                && !$this->get('core.security')->hasExtension('CRONICAS_MODULES')
            ) {
                continue;
            }

            $relations = $rm->getRelations($article->id, $key);

            if (empty($relations)) {
                continue;
            }

            $extra[$name] = array_map(function ($content) {
                return \Onm\StringUtils::convertToUtf8($content);
            }, $em->findMulti($relations));

            $extra[$name] = $fm->set($extra[$name])
                ->filter('localize', [ 'keys' => [ 'title' ] ])
                ->get();

            $article->{$name} = array_map(function ($a) {
                return $a[1];
            }, $relations);
        }

        return $extra;
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
        $em    = $this->get('entity_repository');
        $extra = [];
        $keys  = [ 'fk_video', 'fk_video2' ];

        foreach ($keys as $key) {
            if (!empty($article->{$key})) {
                $extra[$key] = \Onm\StringUtils::convertToUtf8(
                    $em->find('Video', $article->{$key})
                );
            }
        }

        return $extra;
    }
}
