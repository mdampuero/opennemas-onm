<?php
/**
 * This file is part of the Onm package.
 *
 * (c) Openhost, S.L. <developers@opennemas.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
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
     * Returns the list of configurations for uri generation.
     *
     * @return array the array of configurations
     */
    public function getConfig()
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
        ];
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

        $url = '';

        if (is_array($params)
            && array_key_exists('absolute', $params)
            && $params['absolute']
        ) {
            // Absolute URL basing on the current instance
            $url = ($this->forceHttp ? 'http://' : '//')
                . $this->instance->getMainDomain();

            $request = $this->container->get('request_stack')
                ->getCurrentRequest();

            if (!empty($request)) {
                // Absolute URL basing on the current request
                $url = $request->getSchemeAndHttpHost();
            }
        }

        return $url . '/' . $this->getUriForContent($content);
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
        return $this->generateUriFromConfig('article', [
            'id'       => sprintf('%06d', $content->id),
            'date'     => date('YmdHis', strtotime($content->created)),
            'category' => $content->category_name,
            'slug'     => urlencode($content->slug),
        ]);
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
        // If the content has a bodyLink parameter then that it is the final uri.
        if (isset($content->params['bodyLink']) && !empty($content->params['bodyLink'])) {
            return 'redirect?to=' . urlencode($content->params['bodyLink']);
        }

        $methodName = 'getUriFor' . ucfirst($content->content_type_name);

        if (method_exists($this, $methodName)) {
            return $this->{$methodName}($content);
        }

        return $this->generateUriFromConfig(strtolower($content->content_type_name), [
            'id'       => sprintf('%06d', $content->id),
            'date'     => date('YmdHis', strtotime($content->created)),
            'category' => urlencode($content->category_name),
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
        return $this->generateUriFromConfig('letter', [
            'id'       => sprintf('%06d', $content->id),
            'date'     => date('YmdHis', strtotime($content->created)),
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

        if (is_object($content->author)
            && is_array($content->author->meta) &&
            array_key_exists('is_blog', $content->author->meta) &&
            $content->author->meta['is_blog'] == 1
        ) {
            $type = 'blog';
        }

        $authorName = 'author';
        if ($content->fk_author == 0) {
            if ((int) $content->type_opinion == 1) {
                $authorName = 'editorial';
            } elseif ((int) $content->type_opinion == 2) {
                $authorName = 'director';
            }
        } else {
            $authorAux = $content->author;
            if (!is_object($authorAux)) {
                $authorAux = $this->container->get('user_repository')
                    ->find($content->fk_author);
            }

            if (is_object($authorAux)) {
                $authorName = $authorAux->name;
            }
        }

        $authorName = \Onm\StringUtils::generateSlug($authorName);

        return $this->generateUriFromConfig($type, [
            'id'       => sprintf('%06d', $content->id),
            'date'     => date('YmdHis', strtotime($content->created)),
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
        $pathFile    = trim(rtrim($content->path_file, DS), DS);
        $contentName = trim(rtrim($content->name, DS), DS);

        return implode(DS, [
            "media",
            $this->instance->internal_name,
            'images',
            $pathFile,
            $contentName
        ]);
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
}
