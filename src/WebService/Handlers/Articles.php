<?php
/**
 * This file is part of the onm package.
 * (c) 2009-2011 OpenHost S.L. <contact@openhost.es>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WebService\Handlers;

use Luracast\Restler\RestException;

/**
 * Handles REST actions for articles.
 *
 * @package WebService
 */
class Articles
{
    /**
     * Get a complete article
     *
     * @param $id the id of the requested article
     *
     * @return $article
     *
     * @throws RestException
     */
    public function complete($id = null)
    {
        $this->validateInt($id);

        $article = getService('content_url_matcher')
            ->matchContentUrl('article', $id);

        if (empty($article)) {
            throw new RestException(404, 'Page not found');
        }

        try {
            $author = getService('api.service.author')
                ->getItem($article->fk_author);

            $article->agency = !empty($author) ? $author->name : $article->agency;

            // Prevent errors when trying to search authors in target
            $article->fk_author = null;
        } catch (\Exception $e) {
        }

        $article->external    = 1;
        $article->externalUri = getService('router')
            ->generate('frontend_external_article_show', [
                'category_slug' => get_category_slug($article),
                'slug'          => $article->slug,
                'article_id'    => date('YmdHis', strtotime($article->created)) .
                sprintf('%06d', $article->pk_content),
            ]);


        $related = $this->getRelated($article);

        return serialize([ $article, $related ]);
    }

    /**
     * Validates a number
     *
     * This is used for checking the int parameters
     *
     * @param type $number the number to validate
     *
     * @throws RestException
     */
    private function validateInt($number)
    {
        if (!is_numeric($number)) {
            throw new RestException(400, 'parameter is not a number');
        }
        if (is_infinite($number)) {
            throw new RestException(400, 'parameter is not finite');
        }
    }

    /**
     * Returns the list of contents related to the provided contents.
     *
     * @param Content $article The article to get related contents for.
     *
     * @return array The list of related contents.
     */
    protected function getRelated($article)
    {
        $ids = [];
        if (!empty($article->fk_video2) || !empty($article->img2)) {
            $ids[] = !empty($article->fk_video2)
                ? $article->fk_video2
                : $article->img2;
        }

        $ids = array_merge($ids, array_map(function ($a) {
            return $a['target_id'];
        }, array_filter($article->related_contents, function ($a) {
            return preg_match('/.*_inner/', $a['type']);
        })));

        if (empty($ids)) {
            return [];
        }

        $related = getService('entity_repository')->findBy([
            'pk_content' => [[ 'value' => $ids, 'operator' => 'IN' ]],
        ], [ 'starttime' => 'DESC' ]);

        $related = getService('data.manager.filter')
            ->set($related)
            ->filter('mapify', [ 'key' => 'pk_content' ])
            ->get($related);

        foreach ($related as $r) {
            $r->external    = 1;
            $r->externalUri = getService('core.helper.url_generator')
                ->generate($r, [ 'absolute' => true ]);
        }

        return $related;
    }
}
