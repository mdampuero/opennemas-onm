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
        $total   = $em->countBy($criteria);
        $results = $em->findBy($criteria, $order, $epp, $page);

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

        $article = \Onm\StringUtils::convertToUtf8($article);

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
        $keys  = [ 'withGallery', 'withGalleryFrontpage', 'withGalleryHome' ];

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
        $extra = [];

        $security   = $this->get('core.security');
        $converter  = $this->get('orm.manager')->getConverter('Category');
        $categories = $this->get('orm.manager')
            ->getRepository('Category')
            ->findBy();

        $categories = array_filter($categories, function ($a) use ($security) {
            return $security->hasCategory($a->pk_content_category);
        });

        $extra['categories'] = $converter->responsify($categories);
        array_unshift($extra['categories'], [
            'pk_content_category' => null,
            'title' => $all ? _('All') : _('Select a category...')
        ]);

        $converter = $this->get('orm.manager')->getConverter('User');
        $users     = $this->get('orm.manager')->getRepository('User')
            ->findBy('fk_user_group regexp "^3($|,)|,\s*3\s*,|(^|,)\s*3$"');

        foreach ($users as $user) {
            $user->eraseCredentials();
        }

        $extra['users'] = $converter->responsify($users);
        array_unshift($extra['users'], [
            'id'   => null,
            'name' => $all ? _('All') : _('Select an author...')
        ]);

        $extra['keys']          = \Article::getL10nKeys();
        $extra['multilanguage'] = in_array(
            'es.openhost.module.multilanguage',
            $this->get('core.instance')->activated_modules
        );

        $ls          = $this->get('core.locale');
        $translators = null;
        $default     = $ls->getLocale('frontend');

        $extra['locale'] = $ls->getRequestLocale('frontend');

        if ($this->get('core.security')->hasExtension('es.openhost.module.translation')) {
            $translators = $this->get('setting_repository')->get('translators');
        }

        if (empty($translators)) {
            $translators = [];
        }

        $translators = array_map(function ($a) {
            return $a['to'];
        }, array_filter($translators, function ($a) use ($default) {
            return $a['from'] === $default;
        }));

        $extra['options'] = [
            'default'     => $default,
            'available'   => $ls->getAvailableLocales('frontend'),
            'translators' => array_unique($translators)
        ];

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
        $em    = $this->get('entity_repository');
        $extra = [];
        $keys  = [ 'img1', 'img2' ];

        foreach ($keys as $key) {
            if (!empty($article->{$key})) {
                $extra[$key] = \Onm\StringUtils::convertToUtf8(
                    $em->find('Photo', $article->img1)
                );
            }
        }

        if (is_array($article->params)
            && (array_key_exists('imageHome', $article->params))
            && !empty($article->params['imageHome'])
        ) {
            $extra['img3'] = \Onm\StringUtils::convertToUtf8(
                $em->find('Photo', $article->params['imageHome'])
            );
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
    protected function getRelated($article)
    {
        $em    = $this->get('entity_repository');
        $extra = [];
        $keys  = [ 'frontpage', 'inner', 'home' ];
        $rm    = $this->get('related_contents');

        foreach ($keys as $key) {
            if ($key === 'home'
                && !$this->get('core.security')
                    ->hasExtension('CRONICAS_MODULES')
            ) {
                continue;
            }

            $relations = $rm->getRelations($article->id, $key);

            if (count($relations) === 0) {
                continue;
            }

            $extra[$key] = array_map(function ($content) {
                return \Onm\StringUtils::convertToUtf8($content);
            }, $em->findMulti($relations));
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
