<?php

namespace Common\Core\Component\Helper;

use Common\Model\Entity\Category;

class UrlGeneratorHelper
{
    /**
     * The service container.
     *
     * @var ServiceContainer
     */
    protected $container;

    /**
     * The content helper.
     *
     * @var ContentHelper
     */
    protected $contentHelper;

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
     * The helper to localize the route.
     *
     * @var L10nRouteHelper
     */
    protected $routeHelper;

    /**
     * The router component.
     *
     * @var Router
     */
    protected $router;

    /**
     * Initializes the UrlGeneratorHelper.
     *
     * @param ServiceContainer $container The service container.
     */
    public function __construct($container)
    {
        $this->container     = $container;
        $this->contentHelper = $this->container->get('core.helper.content');
        $this->instance      = $this->container->get('core.globals')->getInstance();
        $this->locale        = $this->container->get('core.locale');
        $this->routeHelper   = $this->container->get('core.helper.l10n_route');
        $this->router        = $this->container->get('router');
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
     * @param Content $content The content to generate the url.
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

        $localize = $this->locale->getRequestLocale('frontend');
        if (array_key_exists('locale', $params) && !empty($params['locale'])) {
            $localize = $params['locale'];
            $uri     .= $this->locale->getSlugs()[$params['locale']] &&
                $this->locale->getSlugs()[$params['locale']] !== $this->locale->getSlug() ?
            '/' . $this->locale->getSlugs()[$params['locale']] :
            '';
        }

        $method = 'getUriForContent';

        if (!$content instanceof \Content) {
            $reflect = new \ReflectionClass($content);
            $method  = 'getUriFor' . $reflect->getShortName();
        }

        if (method_exists($this, $method)) {
            $content = $this->container->get('data.manager.filter')->set($content)
                ->filter('localize', [
                    'keys'   => $this->container->get('api.service.content')->getL10nKeys(),
                    'locale' => $localize
                ])
                ->get();

            $uri .= '/' . $this->{$method}($content, $localize);
        }

        $this->locale->setContext($context);

        if (array_key_exists('_format', $params) && $params['_format'] == 'amp') {
            $uri = preg_replace('@\.html$@', '.amp.html', $uri);
        }


        return $uri;
    }

    /**
     * Generates a route based on the provided item and a list of parameters.
     *
     * @param mixed $item   A content or a route name.
     * @param array $params The list of parameters.
     *
     * @return string The generated URL or null if an error is throw.
     */
    public function getUrl($item = null, array $params = []) : ?string
    {
        if (empty($item)) {
            return null;
        }
        $item = is_string($item) ? $item : $this->contentHelper->getContent($item);

        if (!empty($item->externalUri)) {
            return $item->externalUri;
        }
        $absolute = array_key_exists('_absolute', $params) && $params['_absolute'];
        $escape   = array_key_exists('_escape', $params) && $params['_escape'];
        $isAmp    = array_key_exists('_amp', $params) && $params['_amp'];

        // Remove special parameters
        $params = array_filter($params, function ($a) {
            return strpos($a, '_') !== 0;
        }, ARRAY_FILTER_USE_KEY);
        try {
            $url = is_string($item)
                ? $this->router->generate(
                    $item,
                    $params,
                    $absolute
                        ? \Symfony\Component\Routing\Generator\UrlGeneratorInterface::ABSOLUTE_URL
                        : \Symfony\Component\Routing\Generator\UrlGeneratorInterface::ABSOLUTE_PATH
                ) : $this->generate($item, [
                    'absolute' => $absolute,
                    '_format'  => $isAmp ? 'amp' : null,
                ]);

            $url = $this->container->get('core.decorator.url')->prefixUrl($url);
            return $escape ? rawurlencode($url) : $url;
        } catch (\Exception $e) {
            return null;
        }
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
     * Returns the translated url parameters.
     *
     * @param Array    $params The url/route parameters.
     * @param Content  $content  The content object.
     * @param Category $category The category object.
     *
     * @return Array  $finalParams The translated url parameters.
     */
    public function getTranslatedUrlParams($params, $content, $category)
    {
        $slugs       = $this->locale->getSlugs();
        $finalParams = [];

        foreach ($params as $key => $value) {
            if (in_array($key, [ 'category_slug', 'category' ])) {
                $item = $category;
            } elseif ($key === 'slug') {
                $item = $content;
            } else {
                $item = $value;
            }

            foreach (array_keys($slugs) as $longSlug) {
                $finalParams[$key][$longSlug] = $this->getTranlatedSlug($item, $longSlug);
            }
        }

        return $finalParams;
    }

    protected function getTranlatedSlug($item, $longSlug)
    {
        if (!is_object($item)) {
            return $item;
        }

        $propertyName = $item->slug ? 'slug' : 'name';

        $value = $this->container->get('data.manager.filter')->set($item)
            ->filter('localize', [
                'keys'   => [ $propertyName ],
                'locale' => $longSlug
            ])->get();

        return $value->$propertyName;
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
    protected function getUriForAttachment($content, $locale = null)
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
    protected function getUriForArticle($content, $locale = null)
    {
        $created = is_object($content->created)
            ? $content->created->format('Y-m-d H:i:s')
            : $content->created;

        try {
            $category = $this->container->get('api.service.category')
                ->getItem($content->categories[0]);
            $category = $this->container->get('data.manager.filter')->set($category)
                ->filter('localize', [
                    'keys'   => [ 'name' ],
                    'locale' => $locale
                ])
                ->get();

            $categorySlug = $category->name;
        } catch (\Exception $e) {
            $categorySlug = '';
        }

        return $this->generateUriFromConfig('article', [
            'id'            => sprintf('%06d', $content->id),
            'date'          => date('YmdHis', strtotime($created)),
            'category'      => urlencode($categorySlug),
            'slug'          => urlencode($content->slug),
        ]);
    }

    /**
     * Returns the URI for a Category.
     *
     * @param Category $category The category object.
     *
     * @return string The category URI.
     */
    protected function getUriForCategory($category, $locale = null)
    {
        $category = $this->container->get('data.manager.filter')->set($category)
            ->filter('localize', [
                'keys'   => [ 'name' ],
                'locale' => $locale
            ])
            ->get();

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
    protected function getUriForContent($content, $locale = null)
    {
        $methodName = 'getUriFor' . ucfirst($content->content_type_name);

        if (method_exists($this, $methodName)) {
            return $this->{$methodName}($content, $locale);
        }

        try {
            $categoryId = !empty($content->categories)
                ? $content->categories[0]
                : $content->category_id;

            $category = $this->container->get('api.service.category')
                ->getItem($categoryId);
            $category = $this->container->get('data.manager.filter')->set($category)
                ->filter('localize', [
                    'keys'   => [ 'name' ],
                    'locale' => $locale
                ])
                ->get();

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
    protected function getUriForLetter($content, $locale = null)
    {
        $created = is_object($content->created)
            ? $content->created->format('Y-m-d H:i:s')
            : $content->created;

            $content = $this->container->get('data.manager.filter')->set($content)
                ->filter('localize', [
                    'keys'   => $this->container->get('api.service.content')->getL10nKeys(),
                    'locale' => $locale
                ])
                ->get();

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
    protected function getUriForOpinion($content, $locale = null)
    {
        $type = 'opinion';

        $content = $this->container->get('data.manager.filter')->set($content)
            ->filter('localize', [
                'keys'   => $this->container->get('api.service.content')->getL10nKeys(),
                'locale' => $locale
            ])
            ->get();

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

        $authorName = $this->getAuthorName($author);

        return $this->generateUriFromConfig($type, [
            'id'       => sprintf('%06d', $content->pk_content),
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
    protected function getUriForPhoto($content, $locale = null)
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
    protected function getUriForTag($tag, $locale = null)
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
    protected function getUriForUser($user, $locale = null)
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
     * @param User $author The opinion's author.
     *
     * @return string The author name.
     */
    protected function getAuthorName($author)
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
            'company'     => 'empresa/_SLUG_/_DATE__ID_.html',
            'obituary'    => 'esquela/_SLUG_/_DATE__ID_.html',
            'opinion'     => 'opinion/_CATEGORY_/_SLUG_/_DATE__ID_.html',
            'blog'        => 'blog/_CATEGORY_/_SLUG_/_DATE__ID_.html',
            'video'       => 'video/_CATEGORY_/_SLUG_/_DATE__ID_.html',
            'album'       => 'album/_CATEGORY_/_SLUG_/_DATE__ID_.html',
            'poll'        => 'encuesta/_CATEGORY_/_SLUG_/_DATE__ID_.html',
            'static_page' => 'estaticas/_SLUG_.html',
            'ad'          => 'publicidad/_ID_.html',
            'kiosko'      => 'portadas-papel/_CATEGORY_/_DATE__ID_.html',
            'letter'      => 'cartas-al-director/_CATEGORY_/_SLUG_/_DATE__ID_.html',
            'event'       => 'event/_SLUG_',
        ];
    }
}
