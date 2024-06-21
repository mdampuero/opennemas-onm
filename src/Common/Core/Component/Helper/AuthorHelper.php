<?php

namespace Common\Core\Component\Helper;

use Api\Service\V1\AuthorService;
use Common\Core\Component\Template\Template;
use Symfony\Component\Routing\Router;

/**
 * Helper class to retrieve author data.
 */
class AuthorHelper
{
    /**
     * The services container.
     *
     * @var Container
     */

    protected $container;
    /**
     * The author service.
     *
     * @var AuthorService
     */
    protected $service;

    /**
     * The router component.
     *
     * @var Router
     */
    protected $router;

    /**
     * The frontend template.
     *
     * @var Template
     */
    protected $template;

    /**
     * The url generator helper.
     *
     * @var UrlGeneratorHelper
     */
    protected $ugh;

    /**
     * The cache service.
     *
     * @var Cache
     */
    protected $cache;

    /**
     * Initializes the author service.
     *
     * @param Container          $container The service container.
     * @param AuthorService      $service   The author service.
     * @param Template           $template  The frontend template.
     * @param Router             $router    The router.
     * @param UrlGeneratorHelper $ugh       The url generator helper.
     */
    public function __construct(
        $container,
        AuthorService $service,
        Router $router,
        Template $template,
        UrlGeneratorHelper $ugh
    ) {
        $this->container = $container;
        $this->service   = $service;
        $this->template  = $template;
        $this->router    = $router;
        $this->ugh       = $ugh;
        $this->cache     = $this->container->get('cache.connection.instance');
    }

    /**
     * Returns the author for the provided item.
     *
     * @param mixed $item The item to get author for or directly an author. If not
     *                    provided, the function will try to search the item in the
     *                    template.
     *
     * @return \Common\Model\Entity\User The author if present. Null otherwise.
     */
    public function getAuthor($item = null) : ?\Common\Model\Entity\User
    {
        $item = $item ?? $this->template->getValue('item');

        if (empty($item)) {
            return null;
        }

        if ((($item instanceof \Content
            || $item instanceof \Common\Model\Entity\Content)
            && !empty($item->fk_author))
            || (!is_object($item) && is_numeric($item))
        ) {
            try {
                $id      = is_numeric($item) ? $item : $item->fk_author;
                $cacheId = 'author-' . $id;

                $author = $this->cache->get($cacheId);

                if (!empty($author)) {
                    return $author;
                }

                $author = $this->service->getItem($id);

                $this->cache->set($cacheId, $author, 900);

                return $author;
            } catch (\Exception $e) {
                return null;
            }
        }

        return $item instanceof \Common\Model\Entity\User
            ? $item
            : null;
    }

    /**
     * Returns the id for the author avatar.
     *
     * @param mixed $item The item to get author avatar for or an author. If not
     *                    provided, the function will try to search the item in the
     *                    template.
     *
     * @return int The author avatar id.
     */
    public function getAuthorAvatar($item = null) : ?int
    {
        $author = $this->getAuthor($item);

        return !empty($author) ? $author->avatar_img_id : null;
    }

    /**
     * Returns the long author biography for the provided item.
     *
     * @param Content $item The item to get author biography for. If not provided, the
     *                      function will try to search the item in the template.
     *
     * @return string The author bio if author is present. Null otherwise.
     */
    public function getAuthorBioBody($item = null) : ?string
    {
        $author = $this->getAuthor($item);

        return !empty($author->bio_description) ? $author->bio_description : null;
    }

    /**
     * Returns the short author bio for the provided item.
     *
     * @param Content $item The item to get author slug for. If not provided, the
     *                      function will try to search the item in the template.
     *
     * @return string The author bio if author is present. Null otherwise.
     */
    public function getAuthorBioSummary($item = null) : ?string
    {
        $author = $this->getAuthor($item);

        return !empty($author->bio) ? $author->bio : null;
    }

    /**
     * Returns the author id.
     *
     * @param mixed $item The item to get author. If not
     *                    provided, the function will try to search the item in the
     *                    template.
     *
     * @return int The author avatar id.
     */
    public function getAuthorId($item = null) : ?int
    {
        $author = $this->getAuthor($item);

        return !empty($author) ? $author->id : null;
    }

    /**
     * Returns the author title for the provided item.
     *
     * @param Content $item The item to get author name for. If not provided, the
     *                      function will try to search the item in the template.
     *
     * @return string The author name if author present or item agency if item is a
     *                content. Null otherwise.
     */
    public function getAuthorName($item = null) : ?string
    {
        $author = $this->getAuthor($item);

        if (!empty($author)) {
            return $author->name;
        }

        $item = $item ?? $this->template->getValue('item');

        if ($item instanceof \Content
            || $item instanceof \Common\Model\Entity\Content
        ) {
            if (!empty($item->agency)) {
                return $item->agency;
            }

            if (!empty($item->author_name)
                && !$item instanceof \Video
            ) {
                return $item->author_name;
            }
        }

        return null;
    }

    /**
     * Returns the relative URL to the RSS page of the author for the
     * provided item.
     *
     * @param Content $item The item to get URL for. If not provided, the
     *                      function will try to search the item in the template.
     *
     * @return string The rss URL page to the automatic frontpage of the author.
     */
    public function getAuthorRssUrl($item = null) : ?string
    {
        $author = $this->getAuthor($item);

        if (!empty($author)) {
            $routeName   = 'frontend_rss_author';
            $routeParams = [
                'author_slug' => $author->slug,
            ];

            if ($author->inrss) {
                $url = $this->router->generate($routeName, $routeParams);
                $url = $this->container->get('core.decorator.url')->prefixUrl($url);

                return !empty($url) ? $url : null;
            }
        }

        return null;
    }

    /**
     * Returns the author slug for the provided item.
     *
     * @param Content $item The item to get author slug for. If not provided, the
     *                      function will try to search the item in the template.
     *
     * @return string The author slug if author is present. Null otherwise.
     */
    public function getAuthorSlug($item = null) : ?string
    {
        $author = $this->getAuthor($item);

        return empty($author) ? null : $author->slug;
    }

    /**
     * Returns the author twitter url for the provided item.
     *
     * @param Content $item The item to get author facebook url for. If not provided, the
     *                      function will try to search the item in the template.
     *
     * @return string The facebook url if author is present. Null otherwise.
     */
    public function getAuthorSocialFacebookUrl($item = null) : ?string
    {
        $author = $this->getAuthor($item);

        return !empty($author->facebook) ? ("https://www.facebook.com/" . $author->facebook) : null;
    }

    /**
     * Returns the author twitter url for the provided item.
     *
     * @param Content $item The item to get author twitter url for. If not provided, the
     *                      function will try to search the item in the template.
     *
     * @return string The twitter url if author is present. Null otherwise.
     */
    public function getAuthorSocialTwitterUrl($item = null) : ?string
    {
        $author = $this->getAuthor($item);

        return !empty($author->twitter) ? ("https://www.twitter.com/" . $author->twitter) : null;
    }

    /**
     * Returns the author instagram url for the provided item.
     *
     * @param Content $item The item to get author instagram url for. If not provided, the
     *                      function will try to search the item in the template.
     *
     * @return string The instagram url if author is present. Null otherwise.
     */
    public function getAuthorSocialInstagramUrl($item = null) : ?string
    {
        $author = $this->getAuthor($item);

        return !empty($author->instagram) ? ("https://www.instagram.com/" . $author->instagram) : null;
    }

    /**
     * Returns the author linkedin url for the provided item.
     *
     * @param Content $item The item to get author linkedin url for. If not provided, the
     *                      function will try to search the item in the template.
     *
     * @return string The linkedin url if author is present. Null otherwise.
     */
    public function getAuthorSocialLinkedinUrl($item = null) : ?string
    {
        $author = $this->getAuthor($item);

        return !empty($author->linkedin) ? ("https://www.linkedin.com/in/" . $author->linkedin) : null;
    }

    /**
     * Returns the relative URL to the automatic frontpage of the author for the
     * provided item.
     *
     * @param Content $item The item to get URL for. If not provided, the
     *                      function will try to search the item in the template.
     *
     * @return string The relative URL to the automatic frontpage of the author.
     */
    public function getAuthorUrl($item = null) : ?string
    {
        $author = $this->getAuthor($item);

        if (empty($author)) {
            return null;
        }

        if (isset($item->content_type_name) && $item->content_type_name === 'opinion') {
            $url = $this->ugh->generate($author);

            return !empty($author)
                ? $this->container->get('core.decorator.url')->prefixUrl($url)
                : null;
        }

        if ($this->getAuthorSlug($author)) {
            $routeName   = 'frontend_author_frontpage';
            $routeParams = [
                'author_slug' => $this->getAuthorSlug($author),
            ];

            $url = $this->router->generate($routeName, $routeParams);

            return $this->container->get('core.decorator.url')->prefixUrl($url);
        }

        return null;
    }

    /**
     * Checks if there is an author based on a content or author provided as
     * parameter.
     *
     * @param mixed $item The item to check author for or the author. If
     *                    not provided, the function will try to search the item in
     *                    the template.
     *
     * @return bool True if the author exists. False otherwise.
     */
    public function hasAuthor($item = null) : bool
    {
        return !empty($this->getAuthorName($item));
    }

    /**
     * Checks if the author has an avatar based on a content or author provided as
     * parameter.
     *
     * @param mixed $item The item to check author's avatar for or the author. If
     *                    not provided, the function will try to search the item in
     *                    the template.
     *
     * @return bool True if the author has an avatar. False otherwise.
     */
    public function hasAuthorAvatar($item = null) : bool
    {
        return !empty($this->getAuthorAvatar($item));
    }

    /**
     * Checks if the author has a bio defined as
     * parameter.
     *
     * @param mixed $item The item to check author's long bio for or the author. If
     *                    not provided, the function will try to search the item in
     *                    the template.
     *
     * @return bool True if the author has a long biography defined. False otherwise.
     */
    public function hasAuthorBioBody($item = null) : bool
    {
        return !empty($this->getAuthorBioBody($item));
    }

    /**
     * Checks if the author has a bio defined as
     * parameter.
     *
     * @param mixed $item The item to check author's bio for or the author. If
     *                    not provided, the function will try to search the item in
     *                    the template.
     *
     * @return bool True if the author has a short biography defined. False otherwise.
     */
    public function hasAuthorBioSummary($item = null) : bool
    {
        return !empty($this->getAuthorBioSummary($item));
    }

    /**
     * Checks if if the author has a rss url defined based on a content or author
     * provided as parameter
     *
     * @param mixed $item The item to get author rss url property. If not
     *                    provided, the function will try to search the item in the
     *                    template.
     *
     * @return int The author avatar id.
     */
    public function hasAuthorRssUrl($item = null) : bool
    {
        $url = $this->getAuthorRssUrl($item);

        return !empty($url);
    }

    /**
     * Checks if if the author has a slug defined based on a content or author
     * provided as parameter
     *
     * @param mixed $item The item to get author slug property. If not
     *                    provided, the function will try to search the item in the
     *                    template.
     *
     * @return int The author avatar id.
     */
    public function hasAuthorSlug($item = null) : bool
    {
        $slug = $this->getAuthorSlug($item);

        return !empty($slug);
    }

    /**
     * Checks if the author has a facebook account provided as
     * parameter.
     *
     * @param mixed $item The item to check author's facebook account for or the author. If
     *                    not provided, the function will try to search the item in
     *                    the template.
     *
     * @return bool True if the author has a facebook account defined. False otherwise.
     */
    public function hasAuthorSocialFacebookUrl($item = null) : bool
    {
        return !empty($this->getAuthorSocialFacebookUrl($item));
    }

    /**
     * Checks if the author has a twitter account provided as
     * parameter.
     *
     * @param mixed $item The item to check author's twitter account for or the author. If
     *                    not provided, the function will try to search the item in
     *                    the template.
     *
     * @return bool True if the author has a twitter account defined. False otherwise.
     */
    public function hasAuthorSocialTwitterUrl($item = null) : bool
    {
        return !empty($this->getAuthorSocialTwitterUrl($item));
    }

    /**
     * Checks if the author has a instagram account provided as
     * parameter.
     *
     * @param mixed $item The item to check author's instagram account for or the author. If
     *                    not provided, the function will try to search the item in
     *                    the template.
     *
     * @return bool True if the author has a instagram account defined. False otherwise.
     */
    public function hasAuthorSocialInstagramUrl($item = null) : bool
    {
        return !empty($this->getAuthorSocialInstagramUrl($item));
    }

    /**
     * Checks if the author has a linkedin account provided as
     * parameter.
     *
     * @param mixed $item The item to check author's linkedin account for or the author. If
     *                    not provided, the function will try to search the item in
     *                    the template.
     *
     * @return bool True if the author has a linkedin account defined. False otherwise.
     */
    public function hasAuthorSocialLinkedinUrl($item = null) : bool
    {
        return !empty($this->getAuthorSocialLinkedinUrl($item));
    }

    /**
     * Checks if the author has an url based on a content or author provided as
     * parameter.
     *
     * @param mixed $item The item to check author for or the author. If
     *                    not provided, the function will try to search the item in
     *                    the template.
     *
     * @return bool True if the author exists. False otherwise.
     */
    public function hasAuthorUrl($item = null) : bool
    {
        return !empty($this->getAuthorUrl($item));
    }

    /**
     * Checks if if the author is configured as blogger based on a content or author
     * provided as parameter
     *
     * @param mixed $item The item to get author is_blog property. If not
     *                    provided, the function will try to search the item in the
     *                    template.
     *
     * @return int The author avatar id.
     */
    public function isBlog($item = null) : bool
    {
        $author = $this->getAuthor($item);

        return !empty($author->is_blog) ? $author->is_blog : false;
    }
}
