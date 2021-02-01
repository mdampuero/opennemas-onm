<?php

namespace Common\Core\Component\Helper;

class UrlGeneratorHelper
{
    /**
     * The service container.
     *
     * @var ServiceContainer
     */
    protected $container;

    /**
     * The instance to generate URLs for.
     *
     * @var Instance
     */
    protected $instance;

    /**
     * The locale service
     *
     * @var Locale
     */
    protected $locale;

    /**
     * Whether to force HTTP when absolute URLs and no request present.
     *
     * @var boolean
     */
    protected $forceHttp = false;

    /**
     * Initializes the UrlGeneratorHelper.
     *
     * @param ServiceContainer $container The service container.
     */
    public function __construct($container)
    {
        $this->container = $container;
        $this->instance  = $this->container->get('core.instance');
        $this->locale    = $this->container->get('core.locale');
    }

    /**
     * Enables or disables the forced HTTP mode.
     *
     * @param boolean $http The forced HTTP mode value.
     *
     * @return UrlGeneratorHelper The current generator helper.
     */
    public function forceHttp($http)
    {
        $this->forceHttp = $http;

        return $this;
    }

    /**
     * Returns a generated uri for a content type given some params.
     *
     * @param string $content The content to generate the url.
     * @param array  $params  The list of params required to generate the URI.
     *
     * @return string The generated URI.
     */
    public function generate($content, $params = [])
    {
        if (!empty($content->externalUri)) {
            return $content->externalUri;
        }

        $uri = '';

        if (is_array($params)
            && array_key_exists('absolute', $params)
            && $params['absolute']
        ) {
            // Absolute URL basing on the current instance
            $uri = $this->forceHttp
                ? 'http://' . $this->instance->getMainDomain()
                : $this->instance->getbaseUrl();
        }

        // Force frontend context for multilanguage
        $context = $this->locale->getContext();
        $this->locale->setContext('frontend');

        $method = 'getUriForContent';

        if (!$content instanceof \Content) {
            $reflect = new \ReflectionClass($content);
            $method  = 'getUriFor' . $reflect->getShortName();
        }

        if (method_exists($this, $method)) {
            $uri .= '/' . $this->{$method}($content);
        }

        $this->locale->setContext($context);

        if (array_key_exists('_format', $params) && $params['_format'] == 'amp') {
            $uri = preg_replace('@\.html$@', '.amp.html', $uri);
        }

        return $uri;
    }

    /**
     * Checks if the provided URI is for the provided item.
     *
     * @param Content $item The item.
     * @param string  $uri  The URI to check.
     *
     * @return boolean True if the URI is valid for the current item.  False
     *                 otherwise.
     */
    public function isValid($item, $uri)
    {
        return $uri === $this->generate($item);
    }

    /**
     * Changes the instance to force URL generation for the specified instance.
     *
     * @param Instance $sintance The instance.
     *
     * @return UrlGeneratorHelper The current helper.
     */
    public function setInstance($instance)
    {
        $this->instance = $instance;

        return $this;
    }

    /**
     * Returns the URI for an attachment.
     *
     * @param Attachment $content The attachment object.
     *
     * @return string The attachment URI.
     */
    protected function getUriForAttachment($content)
    {
        $pathFile = trim(rtrim($content->path, DS), DS);

        return implode(DS, [
            'media',
            $this->instance->internal_name,
            FILE_DIR,
            $pathFile
        ]);
    }

    /**
     * Returns the URI for an article.
     *
     * @param Article $article The article object.
     *
     * @return string The article URI.
     */
    protected function getUriForArticle($content)
    {
        try {
            $category = $this->container->get('api.service.category')
                ->getItem($content->category_id);

            $categorySlug = $category->name;
        } catch (\Exception $e) {
            $categorySlug = '';
        }

        return $this->generateUriFromConfig('article', [
            'id'       => sprintf('%06d', $content->id),
            'date'     => date('YmdHis', strtotime($content->created)),
            'category' => urlencode($categorySlug),
            'slug'     => urlencode($content->slug),
        ]);
    }

    /**
     * Returns the URI for a Category.
     *
     * @param Category $category The category object.
     *
     * @return string The category URI.
     */
    protected function getUriForCategory($category)
    {
        $uri = $this->container->get('router')->generate('category_frontpage', [
            'category_slug' => $category->name
        ]);

        return trim($uri, '/');
    }

    /**
     * Returns the Uri for a given content.
     *
     * @param mixed The content to generate URI for.
     *
     * @return string The generated URI.
     */
    protected function getUriForContent($content)
    {
        $methodName = 'getUriFor' . ucfirst($content->content_type_name);

        if (method_exists($this, $methodName)) {
            return $this->{$methodName}($content);
        }

        try {
            $categoryId = !empty($content->categories)
                ? $content->categories[0]
                : $content->category_id;

            $category = $this->container->get('api.service.category')
                ->getItem($categoryId);

            $categorySlug = $category->name;
        } catch (\Exception $e) {
            $categorySlug = '';
        }

        $created = is_object($content->created)
            ? $content->created->format('Y-m-d H:i:s')
            : $content->created;

        return $this->generateUriFromConfig(strtolower($content->content_type_name), [
            'id'       => sprintf('%06d', $content->id),
            'date'     => date('YmdHis', strtotime($created)),
            'category' => urlencode($categorySlug),
            'slug'     => urlencode($content->slug),
        ]);
    }

    /**
     * Returns the URI for a letter.
     *
     * @param Letter $content The letter object.
     *
     * @return string The letter URI.
     */
    protected function getUriForLetter($content)
    {
        $created = is_object($content->created)
            ? $content->created->format('Y-m-d H:i:s')
            : $content->created;

        return $this->generateUriFromConfig('letter', [
            'id'       => sprintf('%06d', $content->id),
            'date'     => date('YmdHis', strtotime($created)),
            'slug'     => urlencode($content->slug),
            'category' => urlencode(\Onm\StringUtils::generateSlug($content->author)),
        ]);
    }

    /**
     * Returns the URI for an opinion.
     *
     * @param Opinion $content the content.
     *
     * @return string The opinion URI.
     */
    protected function getUriForOpinion($content)
    {
        $type = 'opinion';

        $created = is_object($content->created)
            ? $content->created->format('Y-m-d H:i:s')
            : $content->created;

        try {
            $author = $this->container->get('api.service.author')
                ->getItem($content->fk_author);
        } catch (\Exception $e) {
            $author = null;
        }

        // If the opinion is not for editorial or director
        // and the author is a blog
        if (is_object($author)
            && isset($author->is_blog)
            && $author->is_blog == 1
        ) {
            $type = 'blog';
        }

        $authorName = $this->getAuthorName($content, $author);

        return $this->generateUriFromConfig($type, [
            'id'       => sprintf('%06d', $content->id),
            'date'     => date('YmdHis', strtotime($created)),
            'slug'     => urlencode($content->slug),
            'category' => urlencode($authorName),
        ]);
    }

    /**
     * Returns the url for a photo.
     *
     * @param Photo $content The photo object.
     *
     * @return string The photo URI.
     */
    protected function getUriForPhoto($content)
    {
        $pathFile = trim(rtrim($content->path, DS), DS);

        return implode(DS, [
            "media",
            $this->instance->internal_name,
            $pathFile
        ]);
    }

    /**
     * Returns the URI for a Tag.
     *
     * @param Tag $tag The tag object.
     *
     * @return string The tag URI.
     */
    protected function getUriForTag($tag)
    {
        $uri = $this->container->get('router')
            ->generate('frontend_tag_frontpage', [ 'slug' => $tag->slug ]);

        return trim($uri, '/');
    }

    /**
     * Returns the URI for an User.
     *
     * @param User $user The user object.
     *
     * @return string The user URI.
     */
    protected function getUriForUser($user)
    {
        $routeName   = 'frontend_opinion_author_frontpage';
        $routeParams = [
            'author_slug' => $user->slug,
            'author_id'   => $user->id,
        ];

        if ($user->is_blog) {
            $routeName   = 'frontend_blog_author_frontpage';
            $routeParams = [ 'author_slug' => $user->slug ];
        }

        $uri = $this->container->get('router')->generate($routeName, $routeParams);

        return trim($uri, '/');
    }

    /**
     * Returns a generated uri for a content type given some params.
     *
     * @param string $contentType The content type to generate the URL.
     * @param array  $params      The list of parameters to generate the URL.
     *
     * @return string The generated URL.
     */
    protected function generateUriFromConfig($contentType, $params = [])
    {
        if (!isset($contentType)) {
            return;
        }

        // Gets the URL template for the given contentType
        $config = $this->getConfig();
        if (!array_key_exists($contentType, $config)) {
            return '';
        }

        $keys = $values = [];
        foreach ($params as $tokenKey => $tokenValue) {
            $keys[]   = "@_" . strtoupper($tokenKey) . "_@";
            $values[] = $tokenValue;
        }

        $uriTemplate = $config[$contentType];

        return preg_replace($keys, $values, $uriTemplate);
    }

    /**
     * Returns the author name to use in the URI for an opinion.
     *
     * @param Opinion $opinion The opinion.
     *
     * @return string The author name.
     */
    protected function getAuthorName($opinion, $author)
    {
        if (!empty($author)) {
            return $this->container->get('data.manager.filter')
                ->set($author->name)
                ->filter('slug')
                ->get();
        }

        return 'author';
    }

    /**
     * Returns the list of configurations for uri generation.
     *
     * @return array the array of configurations
     */
    protected function getConfig()
    {
        return [
            'article'     => 'articulo/_CATEGORY_/_SLUG_/_DATE__ID_.html',
            'opinion'     => 'opinion/_CATEGORY_/_SLUG_/_DATE__ID_.html',
            'blog'        => 'blog/_CATEGORY_/_SLUG_/_DATE__ID_.html',
            'video'       => 'video/_CATEGORY_/_SLUG_/_DATE__ID_.html',
            'album'       => 'album/_CATEGORY_/_SLUG_/_DATE__ID_.html',
            'poll'        => 'encuesta/_CATEGORY_/_SLUG_/_DATE__ID_.html',
            'static_page' => 'estaticas/_SLUG_.html',
            'ad'          => 'publicidad/_ID_.html',
            'kiosko'      => 'portadas-papel/_CATEGORY_/_DATE__ID_.html',
            'letter'      => 'cartas-al-director/_CATEGORY_/_SLUG_/_DATE__ID_.html',
            'special'     => 'especiales/_CATEGORY_/_SLUG_/_DATE__ID_.html',
            'book'        => 'libro/_CATEGORY_/_SLUG_/_DATE__ID_.html',
            'event'       => 'event/_SLUG_',
        ];
    }
}
