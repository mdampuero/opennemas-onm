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
     * The current client.
     *
     * @var Client
     */
    protected $container;

    /**
     * Initializes the UrlGeneratorHelper.
     *
     * @param ServiceContainer $contaienr The service container.
     */
    public function __construct($container)
    {
        $this->container = $container;
    }

    /**
     * Returns a generated uri for a content type given some params
     *
     * @param string $content the content to generate the url
     * @param array  $params the list of params required to generate the url
     *
     * @return string the uri generated
     */
    public function generate($content, $params = [])
    {
        $absolute = (is_array($params) && array_key_exists('absolute', $params) && $params['absolute'] === true);

        $url = '';
        if ($absolute) {
            $url = $this->container->get('request_stack')
                ->getCurrentRequest()->getSchemeAndHttpHost();
        }

        return $url.'/'.$this->getUriForContent($content);
    }

    /**
     * Returns the list of configurations for uri generation
     *
     * @param array $params parameters for modify function behaviour.
     *
     * @return array the array of configurations
     */
    public function getConfig($params = [])
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
     * Returns a generated uri for a content type given some params
     *
     * @param string $contentType the content type to generate the url
     * @param array  $params the list of params required to generate the url
     *
     * @return string the uri generated
     */
    private function generateUriFromConfig($contentType, $params = [])
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
            $keys[] = "@_" . strtoupper($tokenKey) . "_@";
            $values[] = $tokenValue;
        }

        $uriTemplate = $config[$contentType];

        return preg_replace($keys, $values, $uriTemplate);
    }

    /**
     * Returns the Uri for a given content
     *
     * @return void
     * @author
     */
    private function getUriForContent($content)
    {
        // If the content has a bodyLink parameter then that it is the final uri.
        if (isset($content->params['bodyLink']) && !empty($content->params['bodyLink'])) {
            return '/redirect?to='.urlencode($content->params['bodyLink']);
        }

        $methodName = 'getUriFor' . ucfirst($content->content_type_name);

        if (method_exists($this, $methodName)) {
            return $this->{$methodName}($content);
        }

        return $this->getUriForGeneralContent($content);
    }

    /**
     * Returns the url for an Attachment
     *
     * @param Content $content the content
     *
     * @return string the content url
     **/
    private function getUriForAttachment($content)
    {
        $pathFile = trim(rtrim($content->path, DS), DS);

        return implode(DS, ["media", INSTANCE_UNIQUE_NAME, FILE_DIR, $pathFile]);
    }

    /**
     * Returns the url for a  content
     * @param Content $content the content
     *
     * @return string the content url
     **/
    private function getUriForArticle($content)
    {
        return $this->generateUriFromConfig('article', [
            'id'       => sprintf('%06d', $content->id),
            'date'     => date('YmdHis', strtotime($content->created)),
            'category' => $content->category_name,
            'slug'     => urlencode($content->slug),
        ]);
    }

    /**
     * Returns the url for an opinion
     *
     * @param Content $content the content
     *
     * @return string the content url
     **/
    private function getUriForOpinion($content)
    {
        $type ='opinion';

        if (is_object($content->author)
            && is_array($content->author->meta) &&
            array_key_exists('is_blog', $content->author->meta) &&
            $content->author->meta['is_blog'] == 1
        ) {
            $type = 'blog';
        }

        if ($content->fk_author == 0) {
            if ((int) $content->type_opinion == 1) {
                $authorName = 'editorial';
            } elseif ((int) $content->type_opinion == 2) {
                $authorName = 'director';
            } else {
                $authorName = 'author';
            }
        } else {
            if (!is_object($content->author)) {
                $content->author = $this->container->get('user_repository')
                    ->find($content->fk_author);
            }

            if (is_object($content->author)) {
                $authorName = $content->author->name;
            } else {
                $authorName = 'author';
            }

            $authorName = $content->author->name;
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
     * Returns the url for a letter
     *
     * @param Content $content the content
     *
     * @return string the content url
     **/
    private function getUriForLetter($content)
    {
        return $this->generateUriFromConfig('letter', [
            'id'       => sprintf('%06d', $content->id),
            'date'     => date('YmdHis', strtotime($content->created)),
            'slug'     => urlencode($content->slug),
            'category' => urlencode(\Onm\StringUtils::generateSlug($content->author)),
        ]);
    }

    /**
     * Returns the url for a photo
     *
     * @param Content $content the content
     *
     * @return string the content url
     **/
    private function getUriForPhoto($content)
    {
        $pathFile = trim(rtrim($content->path_file, DS), DS);
        $contentName = trim(rtrim($content->name, DS), DS);

        return implode(DS, ["media", INSTANCE_UNIQUE_NAME, 'images', $pathFile, $contentName]);
    }

    /**
     * Returns the url for a content
     *
     * @param Content $content the content
     *
     * @return string the content url
     **/
    private function getUriForGeneralContent($content)
    {
        // The rest of content types follow a common pattern
        return $this->generateUriFromConfig(strtolower($content->content_type_name), [
            'id'       => sprintf('%06d', $content->id),
            'date'     => date('YmdHis', strtotime($content->created)),
            'category' => urlencode($content->category_name),
            'slug'     => urlencode($content->slug),
        ]);
    }
}
